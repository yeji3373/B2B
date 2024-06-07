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
use SendEmail\Models\EmailStatusModel;

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
    $this->buyers = new BuyerModel();
    $this->emailStatus = new EmailStatusModel();
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
    helper(['text', 'auth', 'email']);
    
    $ftpFile = new FtpFileController();
    $buyerCurrency = new BuyerCurrencyModel();
    
    $regsiterRules = [
      'buyerName'           => [
        'label'   =>  'Name of the company',
        // 'rules'   =>  'required|min_length[2]|is_unique[buyers.name,buyers.business_number]', // business_number랑 같이 체크?
        'rules'   =>  'required|min_length[2]|is_unique[buyers.name]',
        'errors'  =>  [
          'is_unique' =>  lang('Auth.alreadyData', ['Name of the company']),
        ]
      ],
      'businessNumber'      => [
        'label'   =>  'Business license Number',
        'rules'   =>  'permit_empty|min_length[2]|alpha_dash',
        'errors'  =>  [
          'alpha_dash' => lang('Auth.disallowedCharacters'),
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
        'rules'   =>  'required|min_length[5]|integer'
      ],
      'buyerAddress1'      => [
        'label'   =>  'Address',
        'rules'   =>  'required|min_length[3]'
      ],
      'zipcode'             => [
        'label'   =>  'Postal code',
        'rules'   =>  'permit_empty|min_length[5]|regex_match[/([A-Za-z0-9][\-])/]'
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
      'email'              => [
        'label'   =>  'e-mail',
        'rules'   =>  'required|is_unique[users.email]',
        'errors'  =>  [
          'required'  =>  'The {field} is required. ',
          'is_unique' =>  lang('Auth.alreadyData', ['e-mail']),
        ]
      ],
      'password'           => [
        'rules'   =>  'required|min_length[5]|regex_match[/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]/]',
        'errors'  => [
          'regex_match' => lang('Auth.pwmsg')
        ]
      ],
      'password_confirm'   => [
        'label'   => 'password confirm',
        'rules'   =>  'required|matches[password]'
      ],
    ];

    if ( !$this->validate($regsiterRules) ) {
      return redirect()->back()->withInput()->with('validation', $this->validator);
    }

    $buyerId = NULL;
    $fileName = NULL;

    $buyer = $this->buyers
                ->asArray()
                ->where("replace(name, ' ', '')", str_replace(' ', '', $this->request->getPost('buyerName')), 'both')
                ->orWhere("replace(replace(business_number, ' ', ''), '-', '')", str_replace('-', '', $this->request->getPost('businessNumber')))
                ->first();
                
    if ( !empty($buyer) ) {
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
          'region_ids' => !empty($this->request->getPost('region')) ? implode(',', $this->request->getPost('region')) : '',
          'countries_ids' => !empty($this->request->getPost('country')) ? implode(',', $this->request->getPost('country')) : '',
          'country_id' => $this->request->getPost('buyerRegion'),
          'address' => $this->request->getPost('buyerAddress1').$this->request->getPost('buyerAddress2'),
          'zipcode' => $this->request->getPost('zipcode'),
          'phone' => $this->request->getPost('buyerPhoneCode').'-'.$this->request->getPost('buyerPhone'),
          'certificate_business' => $fileName,
          'regist_ip' => $this->request->getIPAddress()
      ];

      if ( !$this->buyers->save($buyerData) ) {
        return redirect()->back()->withInput()->with('errors', $this->buyers->errors());
      }
      $buyerId = $this->buyers->getInsertID();
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

    if (!$this->users->save($user)) {
			return redirect()->back()->withInput()->with('errors', $this->users->errors());
    }

    $emailStatus['user_idx'] = $this->users->getInsertID();
    $emailStatus['email_idx'] = get_mail_idx();

    $getEmailStatus = $this->emailStatus->where($emailStatus)->first();

    if ( !empty($getEmailStatus) ) {
      return redirect()->back()->withInput()->with('error', lang('Auth.emailAlreadySent'));
    }

    $emailStatus['activate_hash'] = self::makeHash($user['email']);
    $emailStatus['expiring_date'] = date('Y-m-d', strtotime('+7 days'));

    if ( $this->emailStatus->save($emailStatus) ) {
      // send activation email
      send_activation_email($user['email'], $emailStatus['activate_hash']);
    }
    
		// success
    return redirect()->to('login')->with('success', lang('Auth.registrationSuccess'));
	}
  //--------------------------------------------------------------------

	/**
	 * Activate account.
	 */
	public function activateAccount() {
    $emailStatus = $this->emailStatus
                      ->where('activate_hash', $this->request->getGet('token'))
                      ->where('active', 0)
                      ->where('DATE_FORMAT(created_at, "%Y-%m-%d") <=', date('Y-m-d'))
                      ->where('expiring_date >=', date('Y-m-d'))
                      ->first();

    if ( is_null($emailStatus) ) {
      return redirect()->to('login')->with('error', lang('Auth.activationNoUser'));
    }

		// update user account to active
		$updatedemailStatus['email_status_idx'] = $emailStatus['email_status_idx'];
		$updatedemailStatus['active'] = 1;
		if ($this->emailStatus->save($updatedemailStatus) ) {
      $user = $this->users
                    ->where('idx', $emailStatus['user_idx'])
                    ->where('active', 0)
                    ->first();
      
      if ( empty($user) ) {
        echo lang('Auth.wrongCredentials');
        return;        
      } else {
        $updatedUser['idx'] = $user['idx'];
        $updatedUser['active'] = 1;

        if ( $this->users->save($updatedUser) ) {
          $buyer = $this->buyers
                      ->where('id', $user['buyer_id'])
                      ->where('confirmation', 0)
                      ->where('available', 0)
                      ->first();
          
          $updateBuyer['id'] = $buyer['id'];
          $updateBuyer['confirmation'] = 1;
          $updateBuyer['available'] = 1;

          if ( !$this->buyers->save($updateBuyer) ) {
            echo view('Auth/emails/activation_err', ['title' => lang('Auth.invalidRequest')]);
          }
        }        
      }
    } else {
      echo view('Auth/emails/activation_err', ['title' => lang('Auth.invalidRequest')]);
    }
    
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

  public function makeHash($email = NULL) {
    $encrypter = service('encrypter');
    
    $encryption_key = $encrypter->key;
    $token = bin2hex(random_string('alnum', 32).$email);
    $hash = hash_hmac('sha256', $token, $encryption_key);

    return $hash;
  }
}