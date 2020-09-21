@extends('layouts.frontend')

@section('content')

<div class="container ">


  <div class="g-max-width-645 text-center mx-auto ">
    <br>
    <h4 class="h1 mb-3">Seleccione el medio de pago</h4>
    <p class="g-font-size-17 mb-0">Complete el siguiente formulario y haga click en "Confirmar mi información de comprador".</p>
    <br>
  </div>

  <!-- New order Form -->  
  <div class="row justify-content-center">
    <div class="col-lg-8">
      <form action="{{ route('frontend.new_order') }}" method="post">
        {{ csrf_field() }}
       

     

        <div class="text-center">
          <button class="btn u-btn-primary g-font-size-12 text-uppercase g-py-12 g-px-25" type="submit">Confirmar mi información de comprador</button>
        </div>
      </form>
    </div>
  </div>


<br>
<br>
<br>

  <!-- End order Form -->
</div>


@endsection
