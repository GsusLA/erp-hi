<!DOCTYPE html>
<html lang="es">
<head>
    <link href="/img/company-icon.png" rel="shortcut icon" type="image/x-icon">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>SAO</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="<?php echo e(asset('css/app.css')); ?>" rel="stylesheet"/>
    <style>
        #content {
            width: 100%; height: 100%;
            background-color: #d2d6de;
            position: absolute; top: 0; left: 0;
        }
    </style>
</head>
<body>
<div id="content">
    <div class="login-box">
        <div class="login-logo">
            <img src="<?php echo e(URL::asset('/img/logo_hc.png')); ?>" class="img-responsive img-rounded" width="100%">
        </div>

        <div class="card">
            <div class="card-body login-card-body">
                <p class="login-box-msg">Iniciar Sesión</p>

                <form method="POST" action="<?php echo e(route('login')); ?>">
                    <?php echo e(csrf_field()); ?>

                    <div class="input-group mb-3">
                        <input type="text" name="usuario" class="form-control<?php echo e($errors->has('usuario') ? ' is-invalid' : ''); ?>" placeholder="Usuario" value="<?php echo e(old('usuario')); ?>" required autofocus>
                        <div class="input-group-append">
                            <span class="fa fa-user input-group-text"></span>
                        </div>
                        <?php if($errors->has('usuario')): ?>
                            <span class="invalid-feedback" role="alert">
                                <?php echo e($errors->first('usuario')); ?>

                            </span>
                        <?php endif; ?>
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" name="clave" class="form-control<?php echo e($errors->has('clave') ? ' is-invalid' : ''); ?>" placeholder="Contraseña" required>
                        <div class="input-group-append">
                            <span class="fa fa-lock input-group-text"></span>
                        </div>
                        <?php if($errors->has('clave')): ?>
                            <span class="invalid-feedback" role="alert">
                                <?php echo e($errors->first('clave')); ?>

                            </span>
                        <?php endif; ?>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-block btn-flat">Entrar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo e(asset('js/login.js')); ?>"></script>
</body>
</html>