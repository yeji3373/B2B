<?php
namespace Auth\Controllers;

use CodeIgniter\Controller;
use Config\Email;
use Config\Services;
use Auth\Models\UserModel;
use App\Models\RegionModel;
use App\Models\CountryModel;
use App\Models\BuyerModel;
use App\Models\BuyerCurrencyModel;

use App\Controllers\Order;
use FtpFile\Controllers\FtpFileController;
use VerifyEmail\Controllers\VerifyemailController;

class RegistrationController extends Controller
{
	/**
	 * Access to current session.
	 *
	 * @var \CodeIgniter\Session\Session
	 */
	protected $session;

	/**
	 * Authentication settings.
	 */
	protected $config;

  protected $uploadUrl;

  protected $helpers = ['form'];

  //--------------------------------------------------------------------

	public function __construct()
	{
		// start session
		$this->session = Services::session();

		// load auth settings
		$this->config = config('Auth');
    $this->uploadUrl = "C:\\Apache24\\htdocs\\FILES\\Certificate\\";

    $this->users = new UserModel();
	}

    //--------------------------------------------------------------------

	/**
	 * Displays register form.
	 */
	public function register()
	{
		if ($this->session->isLoggedIn) {
			return redirect()->to('account');
		}
    
    $counties = new CountryModel();
    $region = new RegionModel();
    $itu = new Order();

    $data['config'] = $this->config;
    $data['regions'] = $region->getRegion();
    $data['countries'] = $counties->where('available', 1)->orderBy('sort ASC, region_id ASC, name_en ASC')->findAll();
    $data['itus'] = $counties->select('id, country_no')->orderBy('country_no ASC', 'country_no_sub ASC')->groupBy('country_no')->findAll();
    
    // echo $counties->getLastQuery();
    // echo "<br/>";
    // print_r($data['itus']);
    // $country = new CountryModel();
    // return ;

		// return view($this->config->views['register'], ['config' => $this->config]);
    return view($this->config->views['register'], $data);
	}

    //--------------------------------------------------------------------

	/**
	 * Attempt to register a new user.
	 */
	public function attemptRegister()	{
    $buyers = new BuyerModel();
    $ftpFile = new FtpFileController();
    $buyerCurrency = new BuyerCurrencyModel();

    $regsiterRules = [
      'buyerName'           => [
        'label'   =>  'Name of the company',
        'rules'   =>  'required|min_length[2]|is_unique[buyers.name]',
        'errors'  =>  [
          'is_unique' =>  lang('Auth.alreadyData', ['Name of the company']),
        ]
      ],
      'businessNumber'      => [
        'label'   =>  'Business license Number',
        'rules'   =>  'permit_empty|min_length[2]|regex_match[/([A-Za-z0-9][\s\-])/]',
        'errors'  =>  [
          'regex_match' =>  '',
        ]
      ],
      'buyerRegion'        => [
        'label'   =>  'Country/Region',
        'rules'   =>  'required'
      ],
      'buyerPhoneCode'     => [
        'label'   =>  'Country Code',
        'rules'   =>  'required'
      ],
      'buyerPhone'         => [
        'label'   => 'Phone Number',
        'rules'   =>  'required|min_length[5]|max_length[10]|regex_match[/([0-9])/]'
      ],
      'buyerAddress1'      => [
        'label'   =>  'Address',
        'rules'   =>  'required|min_length[3]'
      ],
      'zipcode'             => [
        'label'   =>  'Postal code',
        'rules'   =>  'permit_empty|min_length[5]|regex_match[/([A-Za-z0-9])/]'
      ],
      'certificateBusiness' => [
        'label'   =>  'Business license/Business card',
        'rules'   =>  'permit_empty|uploaded[certificateBusiness]|mime_in[certificateBusiness, image/jpeg,image/png,image/gif,application/pdf]'
      ],
      'buyerWeb'            => [
        'lable'   =>  'Site url/SNS url',
        'rules'   =>  'permit_empty|min_length[8]|valid_url_strict'
      ],
      'name'               => [
        'label'   =>  'Contact Name',
        'rules'   =>  'required'
      ],
      'verified'            => [
        'label'   =>  'e-mail',
        'rules'   =>  'required',
        'errors'  =>  [
          'required'  => lang('Auth.emailVerified'),
        ]
      ],
      'email'              => [
        'label'   =>  'e-mail',
        'rules'   =>  'required|is_unique[users.email]|valid_email',
        'errors'  =>  [
          'required'  =>  'The {field} is required. ',
          'is_unique' =>  lang('Auth.alreadyData', ['e-mail']),
        ]
      ],
      'password'           => [
        'rules'   =>  'required|min_length[5]|regex_match[/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]/]'
      ],
      'password_confirm'   => [
        'label'   => 'password confirm',
        'rules'   =>  'required|matches[password]'
      ],
    ];

    if ( !$this->validate($regsiterRules) ) {
      return redirect()->back()->withInput()->with('validation', $this->validator);
    }

    if ( empty($this->request->getPost('email')) ) {
      return redirect()->back()->withInput()->with('error', lang('Auth.emailVerified'));
    }

    if ( empty($this->request->getPost('verified')) ) return redirect()->back()->withInput()->with('error', lang('Auth.emailNotAvailable'));

    helper('text');
    $buyerId;
    $fileName = NULL;

    $buyer = $buyers
                ->asArray()
                ->where("replace(name, ' ', '')", str_replace(' ', '', $this->request->getPost('buyerName')), 'both')
                ->orWhere("replace(replace(business_number, ' ', ''), '-', '')", str_replace('-', '', $this->request->getPost('businessNumber')))
                ->first();
    if ( !empty($buyer) ) {
      $buyerId = $buyer['id'];
      return redirect()->back()->withInput()->with('error', lang('Auth.alreadyRegistered'));
    } else {
      if ( isset($_FILES) && !empty($_FILES['certificateBusiness']['name'])) {
        if ( $_FILES['certificateBusiness']['size'] <= 1572864 ) {
          $fileName = str_replace(str_split('\\/:*?"<>|'), '', $this->request->getPost('businessNumber'));
          $ftpFile->fileUpload($_FILES['certificateBusiness'], $fileName);
          $ext = strtolower(pathinfo($_FILES['certificateBusiness']['name'])['extension']);

          $fileName = $fileName.".".$ext;
        } else return redirect()->back()->withInput()->with('error', 'Files Size Error');
      }

      $buyerData = [
          'name'  => $this->request->getPost('buyerName'),
          'business_number' => $this->request->getPost('businessNumber'),
          'region_ids' => implode(',', $this->request->getPost('region')),
          'countries_ids' => !empty($this->request->getPost('country')) ? implode(',', $this->request->getPost('country')) : '',
          'country_id' => $this->request->getPost('buyerRegion'),
          'address' => $this->request->getPost('buyerAddress1').$this->request->getPost('buyerAddress2'),
          'zipcode' => $this->request->getPost('zipcode'),
          'phone' => $this->request->getPost('buyerPhoneCode').'-'.$this->request->getPost('buyerPhone'),
          'certificate_business' => $fileName,
          'regist_ip' => $this->request->getIPAddress()];
      var_dump($buyerData);
      if ( !$buyers->save($buyerData) ) {
        return redirect()->back()->withInput()->with('errors', $buyers->errors());
      }
      $buyerId = $buyers->getInsertID();
    }

		// save new user, validation happens in the model		
		$getRule = $this->users->getRule('registration');
		$this->users->setValidationRules($getRule);
    $user = [
      // 'id'                => $this->request->getPost('id'),
      'name'          	  => $this->request->getPost('name'),
      'email'         	  => $this->request->getPost('email'),
      'password'     		  => $this->request->getPost('password'),
      'password_confirm'	=> $this->request->getPost('password_confirm'),
      'buyer_id'          => $buyerId,
    ];
    // var_dump($user);

    if (!$this->users->save($user)) {
			return redirect()->back()->withInput()->with('errors', $this->users->errors());
    }

		// send activation email
		// helper('auth');
    // send_activation_email($user['email'], $user['activate_hash']);

		// success
    return redirect()->to('login')->with('success', lang('Auth.registrationSuccess'));
	}
  //--------------------------------------------------------------------

	/**
	 * Activate account.
	 */
	public function activateAccount() {
		// $users = new UserModel();

		// check token
		$user = $this->users->where('activate_hash', $this->request->getGet('token'))
                  ->where('active', 0)
                  ->first();

		if (is_null($user)) {
			return redirect()->to('login')->with('error', lang('Auth.activationNoUser'));
		}

		// update user account to active
		$updatedUser['id'] = $user['id'];
		$updatedUser['active'] = 1;
		$this->users->save($updatedUser);

		return redirect()->to('login')->with('success', lang('Auth.activationSuccess'));
	}

  public function certificateFile() {
    $file = $this->request->getFile('certificateBusiness');

    if ( isset($_FILES) ) {
      $fileName = $_FILES['certificateBusiness']['name'];
      $fileInfo = pathinfo($fileName);
      $ext = strtolower($fileInfo['extension']);
      $uploadFile = date('Ymd').'_'.time().'.'.$ext;

      if ( is_uploaded_file($_FILES['certificateBusiness']['tmp_name']) ) {
        if ( !move_uploaded_file($_FILES['certificateBusiness']['tmp_name'], $this->uploadUrl.$uploadFile)) {
          return '';
        } else {
          @chmod($this->uploadUrl.$uploadFile, 0777 & ~umask());
          return $uploadFile;
        } 
      }
    }
  }

  public function verifyCheckJS() {
    $msg = lang('Auth.emailAvailable');
    $verified = TRUE;
    $email = $this->request->getGet('email');    

    if ( !empty($this->users->where('email', $email)->findAll()) ) {
      $verified = FALSE;
      $msg = lang('Auth.alreadyData', ['e-mail']);
    } else {
      $verified = self::VerifyEmailCheck($email);
      if ( $verified === FALSE ) {
        $msg = lang('Auth.emailVerified');
      }
    }

    return json_encode(['verify' => $verified
                      , 'msg' => $msg, 'email' => $email ]);
  }

  /**
  * Verify email
  */
  public function VerifyEmailCheck($email) {
    $verifyEmail = new VerifyemailController();
    
    $verifyEmail->setStreamTimeoutWait(20);
    // $verifyEmail->Debug = TRUE;
    // $verifyEmail->Debugoutput = 'html';
    $verifyEmail->setEmailFrom('mlee5971@beautynetkorea.com');

    $checkEmail = $email;

    if ( $verifyEmail->check($checkEmail) ) {
      return TRUE;
    }
    return FALSE;
  }
}