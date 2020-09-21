<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;

use App\User;
use App\Order;
use App\BankData;

class BankTransfersDataController extends Controller
{

	public function index()
	{
		$datos = BankData::first();
		return View('backend.bank.show', [ 'datos' => $datos, 'title_page' => 'Datos cuenta bancaria']);
	}

    public function edit($id){
        $datos = BankData::first();
		return View('backend.bank.edit', [ 'datos' => $datos, 'title_page' => 'Modificar datos cuenta bancaria']);
    }
	
	public function update()
    {


        #$bank = BankData::findOrfail($id);
        $rules = [
            'name' => 'required|max:255',
            'email' => 'required|max:255',
            'cbu' => 'required|max:255',
            'alias' => 'required|max:255',
          ];
      
          $validator = Validator::make(Input::all(), $rules);
      
          if ($validator->fails())
          {
              return redirect('/backend/transfers_data/edit/'.Input::get('id'))->withErrors($validator)->withInput();
          }
      
            $bank               = BankData::findOrFail(Input::get('id'));
            $bank->name         = Input::get('name');
            $bank->email        = Input::get('email');
            $bank->cbu          = Input::get('cbu');
            $bank->alias        = Input::get('alias');
            
            $bank->save();
      
            return Redirect::to('/backend/transfers_data')->withInput()->with('success', 'Datos actualizados.');

    }

}
