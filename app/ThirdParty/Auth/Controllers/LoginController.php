<?php
namespace Auth\Controllers;

use CodeIgniter\Controller;
use Config\Email;
use Config\Services;
use Auth\Models\UserModel;
use App\Models\BuyerModel;
use App\Models\CurrencyModel;
use App\Models\BuyerCurrencyModel;

class LoginController extends Controller
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


  //--------------------------------------------------------------------

	public function __construct()
	{
		// start session
		$this->session = Services::session();

		// load auth settings
		$this->config = config('Auth');
	}

  //--------------------------------------------------------------------

	/**
	 * Displays login form or redirects if user is already logged in.
	 */
	public function login() {
		if ($this->session->isLoggedIn) {
			// return redirect()->to('account');
      return redirect()->to('/');
		}

		return view($this->config->views['login'], ['config' => $this->config]);
	}

    //--------------------------------------------------------------------

	/**
	 * Attempts to verify user's credentials through POST request.
	 */
	public function attemptLogin() {
		// validate request
		$rules = [
			// 'id'		    => 'required|min_length[2]',
      'email'     => 'required|valid_email',
			'password' 	=> 'required|min_length[5]',
		];

		if (! $this->validate($rules)) {
			return redirect()->to('login')
				->withInput()
				->with('errors', $this->validator->getErrors());
		}

		// check credentials
		$users = new UserModel();
		$user = $users
              // ->where('id', $this->request->getPost('id'))
              ->where('email', $this->request->getPost('email'))
              ->first();
		if ( is_null($user) || !password_verify($this->request->getPost('password'), $user['password']) ) {
			return redirect()->to('login')->withInput()->with('error', lang('Auth.wrongCredentials'));
		}

		// check activation
		if (!$user['active']) {
			return redirect()->to('login')->withInput()->with('error', lang('Auth.notActivated'));
		} else {
      $buyers = new BuyerModel();
      // print_r($buyers);
      // print_r($buyers->table);
      $buyer = $buyers->buyerJoin()
                ->where(["{$buyers->table}.id" => $user['buyer_id']])
                ->first();
      if (empty($buyer)) {
        return redirect()->to('login')->withInput()->with('error', lang('Auth.notActivated'));
      } else {
        if ( $buyer['confirmation'] != 1 ) {
          return redirect()->to('login')->withInput()->with('error', lang('Auth.confirmationNoBuyer'));
        } else {
          $buyerCurrencies = new BuyerCurrencyModel();
          $currencies = new CurrencyModel();

          $buyerCurrency = $buyerCurrencies->where(['buyer_id'=> $buyer['id'], 'available' => 1])->first();
          if ( !empty($buyerCurrency) ) {
            $currency = $currencies
                          ->select('currency.*')
                          ->select('currency_rate.cRate_idx, currency_rate.exchange_rate')
                          ->select('CR.cRate_idx AS based_cRate_idx, CR.exchange_rate AS based_exchange_rate')
                          ->currencyJoin()
                          ->join('currency_rate AS CR', '(CR.currency_idx = currency.idx AND CR.default_set = 1 AND CR.available = 1)')
                          ->where(["currency_rate.cRate_idx"=> $buyerCurrency['cRate_idx']])->first();
          } else {
            $currency = $currencies->currencyJoin()
                          ->select("{$currencies->table}.*")
                          ->select("currency_rate.cRate_idx, currency_rate.exchange_rate, currency_rate.default_set")
                          ->where(["{$currencies->table}.default_currency" => 1, "{$currencies->table}.available" => 1, 'currency_rate.default_set' => 1])
                          ->first();
            
            // if ( empty($currency) ) {
            //   $currency = $currencies->currencyJoin()
            //                 ->select("{$currencies->table}.*")
            //                 ->select("currency_rate.cRate_idx, currency_rate.exchange_rate, currency_rate.default_set")
            //                 ->where(["{$currencies->table}.default_currency" => 1, "currency_rate.default_set" => 1, "currency_rate.available" => 1])
            //                 ->first();
            // }
          }

          if ( !empty($currency) ) {
            if ( empty($currency['based_exchange_rate']) ) {
              $currency['based_exchange_rate'] = $currency['exchange_rate'];
              $currency['preferential_rate'] = 0;
            } else $currency['preferential_rate'] = 1;
            // if ( empty($currency['currency_rate_idx'])) {
              $this->session->set('currency', [
                'currencyId'        => $currency['idx'],
                'currencyUnit'      => $currency['currency_code'],
                'currencySign'      => $currency['currency_sign'],
                'basedExchangeRate' => $currency['based_exchange_rate'],
                'exchangeRate'      => $currency['exchange_rate'],
                'currencyFloat'     => $currency['currency_float'],
                'preferentialRate'  => $currency['preferential_rate'],
              ]);
            // } else {
            //   $this->session->set('currency', [
            //     'currencyId'      => $currency['idx'],
            //     'currencyUnit'    => $currency['currency_code'],
            //     'currencySign'    => $currency['currency_sign'],
            //     'exchangeRate'    => $currency['currency_rate_exchange'],
            //     'currencyFloat'   => $currency['currency_float'],
            //   ]);
            // }
          }
        }
        // print_r(session()->currency);
      }
    }
		// login OK, save user data to session
		$this->session->set('isLoggedIn', true);
		$this->session->set('userData', [
		    // 'id' 			      => $user['id'],
		    'name' 			    => $user['name'],
		    'email' 		    => $user['email'],
        'id'            => $user['idx'],
        'buyerId'       => $buyer['id'],
        'buyerName'     => $buyer['name'],
        'buyerMargin'   => $buyer['margin_level'],
        'depositRate'   => $buyer['deposit_rate'],
		]);
    return redirect()->to('/');
	}

    //--------------------------------------------------------------------

	/**
	 * Log the user out.
	 */
	public function logout() {
		$this->session->remove(['isLoggedIn', 'userData', 'currency']);

		return redirect()->to('login');
	}

}