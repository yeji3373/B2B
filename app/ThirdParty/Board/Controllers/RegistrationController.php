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

  //--------------------------------------------------------------------

	public function __construct()
	{
		// start session
		$this->session = Services::session();

		// load auth settings
		$this->config = config('Auth');
    $this->uploadUrl = "C:\\Apache24\\htdocs\\FILES\\Certificate\\";
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
    $data['countries'] = $counties->where('available', 1)->orderBy('region_id ASC')->findAll();
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
	public function attemptRegister()
	{
		helper('text');
    $users = new UserModel();
    $buyers = new BuyerModel();
    $ftpFile = new FtpFileController();
    $buyerCurrency = new BuyerCurrencyModel();
    $buyerId;
    $fileName;
    
    $buyer = $buyers
                ->asArray()
                ->where("replace(name, ' ', '')", str_replace(' ', '', $this->request->getPost('buyerName')), 'both')
                ->orWhere("replace(replace(business_number, ' ', ''), '-', '')", str_replace('-', '', $this->request->getPost('businessNumber')))
                ->first();
    if ( !empty($buyer) ) {
      $buyerId = $buyer['id'];

      // return redirect()->back()->withInput()->with('error', '이미 등록된 업체(buyer)');
    } else {
      if ( isset($_FILES) ) {
        if ( $_FILES['certificateBusiness']['size'] <= 1572864 ) {
          $fileName = str_replace(str_split('\\/:*?"<>|'), '', $this->request->getPost('businessNumber'));
          $ftpFile->fileUpload($_FILES['certificateBusiness'], $fileName);
        } else return redirect()->back()->withInput()->with('error', 'Files Size Error');
      }
      $buyerData = [
              'name'  => $this->request->getPost('buyerName'),
              'business_number' => $this->request->getPost('businessNumber'),
              'region_ids' => implode(',', $this->request->getPost('region')),
              'countries_ids' => implode(',', $this->request->getPost('country')),
              'country_id' => $this->request->getPost('buyerRegion'),
              'address' => $this->request->getPost('buyerAddress1').$this->request->getPost('buyerAddress2'),
              'zipcode' => $this->request->getPost('zipcode'),
              'phone' => $this->request->getPost('buyerPhoneCode').'-'.$this->request->getPost('buyerPhone'),
              'certificate_business' => $fileName
      ];
      if ( !$buyers->save($buyerData)) {
        return redirect()->back()->withInput()->with('errors', $buyers->errors());
      }
      $buyerId = $buyers->getInsertID();
    }

		// // save new user, validation happens in the model		
		$getRule = $users->getRule('registration');
		$users->setValidationRules($getRule);
    $user = [
      'name'          	  => $this->request->getPost('name'),
      'id'                => $this->request->getPost('id'),
      'email'         	  => $this->request->getPost('email'),
      'password'     		  => $this->request->getPost('password'),
      'password_confirm'	=> $this->request->getPost('password_confirm'),
      'buyer_id'          => $buyerId,
    ];

    if (!$users->save($user)) {
			return redirect()->back()->withInput()->with('errors', $users->errors());
    }

		// // // send activation email
		// // helper('auth');
    // // send_activation_email($user['email'], $user['activate_hash']);

		// // success
    return redirect()->to('login')->with('success', lang('Auth.registrationSuccess'));
	}
  //--------------------------------------------------------------------

	/**
	 * Activate account.
	 */
	public function activateAccount() {
		$users = new UserModel();

		// check token
		$user = $users->where('activate_hash', $this->request->getGet('token'))
                  ->where('active', 0)
                  ->first();

		if (is_null($user)) {
			return redirect()->to('login')->with('error', lang('Auth.activationNoUser'));
		}

		// update user account to active
		$updatedUser['id'] = $user['id'];
		$updatedUser['active'] = 1;
		$users->save($updatedUser);

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
}