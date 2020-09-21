<?php

namespace App\Http\Controllers;

use Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use App\Category;
use App\Subcategory;
use App\Product;
use App\Order;
use App\Slider;
use App\ImagesProduct;
use Cart;
use Auth;
use MP;
use Mail;
use App\TodoPagoWrap;
use DB;
use MercadoPago;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        #$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['sliders'] = Slider::all();
        $data['categories'] = DB::table('categories')->orderBy('name', 'ASC')->get();
        $data['outstandings'] = Product::where('outstanding', 1)->get();
        $data['lasts_products'] = Product::where('outstanding', 0)->orderBy('id', 'DESC')->take(9)->get();

        return view('frontend_common.home', $data);//return view('home');
    }


    /**
     * Show the application dtempalte.
     *
     * @return \Illuminate\Http\Response
     */
    public function product_detail($id)
    {
        $data['categories'] = DB::table('categories')->orderBy('name', 'ASC')->get();
        $data['product'] = Product::findOrfail($id);
        $data['outstandings'] = Product::where('outstanding', 1)->get();
        return view('frontend_common.product_detail', $data);
    }

    /**
     * Show the application dtempalte.
     *
     * @return \Illuminate\Http\Response
     */
    public function products_list()
    {
        $data['categories'] = DB::table('categories')->orderBy('name', 'ASC')->get();
        $data['products'] = Product::paginate(3);
        $data['outstandings'] = Product::where('outstanding', 1)->get();
        return view('frontend_common.products_list', $data);
    }

    /**
     * Show the application dtempalte.
     *
     * @return \Illuminate\Http\Response
     */
    public function outstandings()
    {
        $data['categories'] = DB::table('categories')->where('outstanding', 0)->orderBy('name', 'ASC')->get();
        $data['products'] = Product::where('outstanding', 1)->paginate(10);

        return view('frontend_common.products_outstandings', $data);
    }



    /**
     * Show the application dtempalte.
     *
     * @return \Illuminate\Http\Response
     */
    public function by_category($id)
    {
        $data['categories'] = DB::table('categories')->where('outstanding', 0)->orderBy('name', 'ASC')->get();
        $data['category'] = Category::find($id);
        $data['products'] = Product::where('category_id', $id)->paginate(3);
        $data['outstandings'] = Product::where('outstanding', 1)->get();
        return view('frontend_common.products_by_category', $data);
    }

    /**
     * Show the application dtempalte.
     *
     * @return \Illuminate\Http\Response
     */
    public function by_subcategory($id)
    {
        $data['categories'] = DB::table('categories')->where('outstanding', 0)->orderBy('name', 'ASC')->get();
        $subcategory = Subcategory::find($id);
        $data['category'] = Category::find($subcategory->category_id);
        $data['subcategory'] = $subcategory;
        $data['products'] = Product::where('subcategory_id', $id)->paginate(3);
        $data['outstandings'] = Product::where('outstanding', 1)->get();
        return view('frontend_common.products_by_subcategory', $data);
    }

    public function cart()
    {
        $categories = DB::table('categories')->orderBy('name', 'ASC')->get();
        //update/ add new item to cart
        if (Request::isMethod('post')) {
            $product_id = Request::get('product_id');
            $product = Product::find($product_id);
            Cart::add(array('id' => $product_id, 'name' => $product->title, 'qty' => 1, 'price' => $product->price));
            return \App::make('redirect')->back()->with('success', 'Producto agregado al carrito.');
        }

        //increment the quantity
        if (Request::get('product_id') && (Request::get('increment')) == 1) {
            $rowId = Cart::search(function ($key, $value) {
                return $key->id == Request::get('product_id');
            });
            $new_quantity = $rowId->first()->qty + 1;
            Cart::update($rowId->first()->rowId, ['qty' => $new_quantity]);
            return \App::make('redirect')->back();
        }

        //decrease the quantity
        if (Request::get('product_id') && (Request::get('decrease')) == 1) {
            $rowId = Cart::search(function ($key, $value) {
                return $key->id == Request::get('product_id');
            });
            $new_quantity = $rowId->first()->qty - 1;
            Cart::update($rowId->first()->rowId, ['qty' => $new_quantity]);
            return \App::make('redirect')->back();
        }

        //Remove item
        if (Request::get('product_id') && (Request::get('delete')) == 1) {
            $rowId = Cart::search(function ($key, $value) {
                return $key->id == Request::get('product_id');
            });
            Cart::remove($rowId->first()->rowId);
            return \App::make('redirect')->back();
        }

        $cart = Cart::content();

        return view('frontend_common.cart', array('cart' => $cart, 'title' => 'Welcome', 'description' => '', 'page' => 'home', 'categories' => $categories));
    }

    public function contact()
    {
        $data['categories'] = DB::table('categories')->orderBy('name', 'ASC')->get();
        $data['outstandings'] = Product::where('outstanding', 1)->get();
        $data['products'] = Product::where('outstanding', 0)->take(9)->get();

        return view('frontend_common.contact', $data);//return view('home');
    }

    public function process_contact()
    {
        $rules = [
            'name' => 'required',
            'email' => 'required',
            'message' => 'required',
        ];

        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails()) {
            return redirect('/contact'.'#contact')->withErrors($validator)->withInput();
        }


        #ContactUS::create($request->all());
        $name       = Input::get('name');
        $email      = Input::get('email');
        $subject    = Input::get('subject');
        $telephone  = Input::get('telephone');
        $user_message    = Input::get('message');

        Mail::send(

            'email',
             array(
                 'name'       => $name,
                 'email'      => $email,
                 'subject'    => $subject,
                 'telephone'  => $telephone,
                 'user_message'    => $user_message
             ),

            function ($message) {
                $message->from('info@hubercart.tk');
                $message->to('hubermann@gmail.com', 'Admin')->subject('Website Feedback');
            }

        );

        return redirect('/contact')->with('success', 'Gracias por su mensaje');
    }


    public static function get_product_images($id)
    {
        return ImagesProduct::where('product_id', $id)->get();
    }

    public static function get_categories_outstandings()
    {
        return Category::where('outstanding', 1)->orderBy('name', 'ASC')->get();
    }

    public static function get_categories()
    {
        return DB::table('categories')->where('outstanding', 0)->orderBy('name', 'ASC')->get();
    }

    public static function get_subcategories($id)
    {
        return Subcategory::where('category_id', $id)->get();
    }

    public static function products_by_category($id)
    {
        return Product::where('category_id', $id)->count();
    }

    public function checkout()
    {
        if (!Auth::user()) {
            return redirect('login')->with('warning', 'Por favor identifiquese.');
        }
        if (Cart::total() == 0.00) {
            return redirect('/')->with('warning', 'No hay productos en su orden.');
        }

        $payment_methods = ['TP', 'deposito'];  //add more in the future

        return view('frontend_common.new_order')
                ->with('states', TodoPagoWrap::get_all_state_code())
                ->with('payment_methods', $payment_methods );
    }


    public function process_new_order()
    {
        $rules = [
            'name' => 'required|max:80',
            'surname' => 'required|max:80',
            'area_code' => 'required|max:50',
            'telephone' => 'required|max:20',
            'street_name' => 'required|max:20',
            'street_number' => 'required|max:20',
            'city' => 'required|max:20',
            'zip_code' => 'required|max:20',
        ];

        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails()) {
            return redirect('/checkout')->withErrors($validator)->withInput();
        }
        $items = [];
        foreach (Cart::content() as $row) {
            array_push($items, ["id" => $row->id, "title" => $row->name, "quantity" => $row->qty, "currency_id" => "ARS", "unit_price" => $row->price ]);
        }

        $order                      = new Order();
        $order->name                = Input::get('name');
        $order->surname             = Input::get('surname');
        $order->area_code           = Input::get('area_code');
        $order->telephone           = Input::get('telephone');
        $order->street_name         = Input::get('street_name');
        $order->street_number       = Input::get('street_number');
        $order->city                = Input::get('city');
        $order->state               = Input::get('state');
        $order->zip_code            = Input::get('zip_code');
        $order->user_id             = Auth::user()->id;
        $order->email               = Auth::user()->email;
        $order->order_description   = serialize($items); 
        $order->payment_status      = Order::PENDING;
        $order->amount              = Cart::total();

        $order->save();

        //enviar el id de orden y luego actuualizarla dependiendo del resultado.

        return view('frontend_common.select_payment', ['order_id' => $order->id]);

    }


    public function process_todo_pago($order_id, $items){
        //El user debe poder ver sus ordenes y si es el estado es sin-pagar debe tener la posibilidad de pagar
        $request = Request::instance();

        $TP = new TodoPagoWrap;

        $TP->client_user_id    = Auth::user()->id;
        $TP->client_email      = Auth::user()->email;
        $TP->client_name       = Input::get('name');
        $TP->client_surname    = Input::get('surname');
        $TP->client_telephone  = Input::get('area_code').Input::get('telephone');
        $TP->client_ip         = $request->getClientIp();

        $TP->receiving_name      = Input::get('name');
        $TP->receiving_surname   = Input::get('surname');
        $TP->receiving_email     = Auth::user()->email;
        $TP->receiving_telephone = Input::get('area_code').Input::get('telephone');

        $TP->shipment_city     = Input::get('city');
        $TP->shipment_state    = Input::get('state');
        $TP->shipment_address  = [
          'street_name'   => Input::get('street_name'),
          'street_number' => Input::get('street_number'),
        ];
        $TP->shipment_zip_code = Input::get('zip_code');

        $TP->billing_city      = Input::get('city');
        $TP->billing_state     = Input::get('state');
        ;
        $TP->billing_address   = [
          'street_name'   => Input::get('street_name'),
          'street_number' => Input::get('street_number'),
        ];
        $TP->billing_zip_code  = Input::get('zip_code');

        $TP->items             = $items;
        $TP->id_order          = $order_id;


        if ($TP->checkout()) { // si pudimos hacer el ticket redirigimos
            return Redirect::to($TP->url_form_pago);
        } else { // como no pudimos crear el tiket debemos mostrar un mensaje, por ejemplo para sugerir reintentar
            return view('frontend_common.checkout_result')->with('message', 'Por favor intente nuevamente luego.');
        }
    }

    //retry payment
    public function retry_process_order($id)
    {
        $order = Order::find($id);

        if ($order->payment_status == 0 || $order->payment_status == 2) {
            $TP = new TodoPagoWrap;

            $order_items = unserialize($order->order_description);

            $TP->client_user_id    = Auth::user()->id;
            $TP->client_email      = Auth::user()->email;
            $TP->client_name       = $order->name;
            $TP->client_surname    = $order->surname;
            $TP->client_telephone  = $order->area_code.$order->telephone;
            $TP->client_ip         = Request::ip();

            $TP->receiving_name      = $order->name;
            $TP->receiving_surname   = $order->surname;
            $TP->receiving_email     = Auth::user()->email;
            $TP->receiving_telephone = $order->area_code.$order->telephone;

            $TP->shipment_city     = $order->city;
            $TP->shipment_state    = $order->state;
            $TP->shipment_address  = [
              'street_name'   => $order->street_name,
              'street_number' => $order->street_number,
            ];
            $TP->shipment_zip_code = $order->zip_code;

            $TP->billing_city      = $order->city;
            $TP->billing_state     = $order->state;
            $TP->billing_address   = [
              'street_name'   => $order->street_name,
              'street_number' => $order->street_number,
            ];
            $TP->billing_zip_code  = $order->zip_code;

            $TP->items             = $order_items;
            $TP->id_order          = $order->id;


            if ($TP->checkout()) { // si pudimos hacer el ticket redirigimos
                return Redirect::to($TP->url_form_pago);
            } else { // como no pudimos crear el tiket debemos mostrar un mensaje, por ejemplo para sugerir reintentar
                return view('frontend_common.checkout_result', ['status' => 0, 'order_id' => $order->id]);
            }
        }
    }



    public function todo_pago_payment_success(Request $request)
    {
        $params   = $request::all();
        $id_order = strip_tags($params['operationid']);

        $TP             = new TodoPagoWrap;
        $feedback_key= strip_tags($params['Answer']);
        $TP->answer_key = $feedback_key;
        if ($TP->payment_success()) { //comprobamos el estado del pago
         // aca tenemos que hacer el procesamiento correspondiente a una orden pagada
            $order = Order::find($id_order);
            $order->feedback_mp = $feedback_key;
            $order->payment_success();

            //notificacion a customer
            Mail::send(
              'new_order_email',
             array(
                 'order_id' => $id_order,
             ),
              function ($message) {
                  $message->from('info@hubercart.tk');
                  $message->to('hubermann@gmail.com', 'Admin')->subject('Nueva orden pagada.');
              }
          );

            //update stock
            foreach (Cart::content() as $item) {
                $product = Product::findOrfail($item->id);
                $product->qty = $product->qty - $item->qty;
                $product->save();
            }

            Cart::destroy();
            return view('frontend_common.checkout_result', ['status' => 1,'order_id' => $id_order]);
        } else { // si llegamos aca es por que no se pudo comprobar el pago
            return redirect('/todo_pago/payment_error?operationid='.$id_order);
        }
    }

    public function todo_pago_payment_error(Request $request)
    {
        $params   = $request::all();
        $id_order = strip_tags($params['operationid']);

        $order = Order::find($id_order)->payment_rejected();
        return view('frontend_common.checkout_result', ['status' => 2, 'order_id' => $id_order]);
    }


    public function user_orders()
    {
        if (!Auth::user()) {
            return redirect('login')->with('warning', 'Por favor identifiquese con su usuario y contraseña.');
        }
        $data['categories'] = Category::all();
        $data['orders'] = Order::where('user_id', Auth::user()->id)->orderBy('id', 'desc')->get();

        return view('frontend_common.user_orders', $data);
    }


    public function informacion_general()
    {
        return view('frontend_common.informacion_general');
    }
}
