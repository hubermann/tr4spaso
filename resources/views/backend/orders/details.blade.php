@extends('layouts.backend')

@section('content')

<!--row -->
<div class="row">
	<div class="col-sm-12">


		<div class="white-box">


				<nav class="navbar navbar-light bg-faded">
				<div class="collapse navbar-collapse" id="navbarNavAltMarkup">
				  <div class="navbar-nav">
				    <a class="nav-item nav-link btn " href="{{route('backend.orders')}}">All <span class="sr-only">(current)</span></a>
				    <a class="nav-item nav-link btn" href="{{route('backend.orders_successfully')}}">Successfully</a>
				    <a class="nav-item nav-link btn" href="{{route('backend.orders_declined')}}">Declined</a>
				    <a class="nav-item nav-link btn" href="{{route('backend.orders_pending')}}">Pending</a>
				  </div>
				</div>
				</nav>
				<h3 class="box-title">{{ $title_page }}</h3>

				<div class="col-md-2 col-sm-4 col-xs-12 pull-right text-right">

					<!-- btn -->

				</div>

			<div class="table-responsive">
                <p><span>Comprador: <strong> {{ $user->name}}</strong></span> | <span> {{ $order->email }} </span>  </p>
                <p><span>ID: {{ $order->id }}</span> - <span>{{ $order->created_at}}</span></p>
                

                <table class="table ">
                <thead>
                    <th class="text-left">ID</th>
                    <th class="text-left">Producto</th>
                    <th class="text-left">Cantidad</th>
                    <th class="text-left">Moneda</th>
                    <th class="text-left">Precio</th>
                </thead>
                <tbody>
                    <tr>
                        @foreach( $items[0] as $item )
                            <td>{{$item}}</td>
                        @endforeach           
                    </tr>             
                </tbody>
                </table>
				
            </div>
            
		</div>

	</div>
</div>
<!-- /.row -->

@endsection
