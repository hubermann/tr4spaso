@extends('layouts.backend')

@section('content')
<!--row -->
<div class="row">
    <div class="col-sm-12">
		<div class="white-box">
            <h3>Modificar datos bancarias para recibir transferencias.</h3>
            

            <form action="{{ route('backend.transfers_data.update') }}" method="post">
				{{ csrf_field() }}
				<fieldset>

                <input type="hidden" name="id" id="id" value="{{$datos->id}}">

                <div class="form-group">
                    <label for="name">Nombre del banco</label>
                    <input type="text" name="name" class="form-control" value="{{ $datos->name }}">
                    @if ($errors->has('name'))
                        <span class="help-block">
                            <strong>{{ $errors->first('name') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="form-group">
                    <label for="email">email</label>
                    <input type="text" name="email" class="form-control" value="{{ $datos->email }}">
                    @if ($errors->has('email'))
                        <span class="help-block">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="form-group">
                    <label for="cbu">CBU</label>
                    <input type="text" name="cbu" class="form-control" value="{{ $datos->cbu }}">
                    @if ($errors->has('cbu'))
                        <span class="help-block">
                            <strong>{{ $errors->first('cbu') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="form-group">
                    <label for="alias">Alias</label>
                    <input type="text" name="alias" class="form-control" value="{{ $datos->alias }}">
                    @if ($errors->has('alias'))
                        <span class="help-block">
                            <strong>{{ $errors->first('alias') }}</strong>
                        </span>
                    @endif
                </div>
                
                <div class="form-group">
                    <button class="btn btn-primary" type="submit">Actualizar</button>
                </div>

                </fieldset>
            </form>       

        </div>
        
    </div>
</div>
<!-- /.row -->

@endsection