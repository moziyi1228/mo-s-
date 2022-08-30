<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Multikart">
    <meta name="keywords" content="Multikart">
    <meta name="author" content="Multikart">
    <link rel="manifest" href="./manifest.json">
    <link rel="icon" href="{{URL::asset('assets1/images/favicon.png')}}" type="image/x-icon" />
    <title>Multikart PWA App</title>
    <link rel="icon" href="{{URL::asset('assets1/images/favicon.png')}}" type="image/x-icon" />
    <link rel="apple-touch-icon" href="{{URL::asset('assets1/images/favicon.png')}}">
    <meta name="theme-color" content="#ff4c3b" />
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-title" content="multikart">
    <meta name="msapplication-TileImage" content="{{URL::asset('assets1/images/favicon.png')}}">
    <meta name="msapplication-TileColor" content="#FFFFFF">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="_token" content="{{csrf_token()}}">

    <!--Google font-->
    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700,900" rel="stylesheet">

    <!-- bootstrap css -->
    <link rel="stylesheet" type="text/css" href="{{URL::asset('assets1/css/vendors/bootstrap.css')}}">

    <!-- slick css -->
    <link rel="stylesheet" type="text/css" href="{{URL::asset('assets1/css/vendors/slick-theme.css')}}">
    <link rel="stylesheet" type="text/css" href="{{URL::asset('assets1/css/vendors/slick.css')}}">

    <!-- iconly css -->
    <link rel="stylesheet" type="text/css" href="{{URL::asset('assets1/css/vendors/iconly.css')}}">

    <!-- Theme css -->
    <link rel="stylesheet" type="text/css"  href="{{URL::asset('assets1/css/style.css')}}">

</head>

<body>

    <!-- loader strat -->
    <div class="loader">
        <span></span>
        <span></span>
    </div>
    <!-- loader end -->


    <!-- header start -->
    <header>
        <div class="back-links">
            <a href="/front/index">
                <i class="iconly-Arrow-Left icli"></i>
                <div class="content">
                    <h2>首页</h2>
                </div>
            </a>
        </div>
    </header>
    <!-- header end -->


    <!-- user avtar section -->
    <section class="user-avtar-section top-space pt-0 px-15" style="position: relative;">
        <input type="file" id="photo"  name="photo" onchange="photoupload(this)" accept="image/png,image/gif,image/jpg" style="position: absolute; top: 0; bottom: 0; left: 0;right: 0; opacity: 0;" >
        <img  id='selfie' src="{{URL::asset('assets1/images/user/1.png')}}" class="img-fluid" alt="">

        <span class="edit-icon"><i class="iconly-Edit-Square icli"></i></span>
    </section>
    <!-- user avtar end -->


    <!-- detail form start -->
    <section class="detail-form-section px-15">
        <h2 class="page-title mb-4">个人信息</h2>
        <form>
            <div class="form-floating mb-4">
                <input type="text" class="form-control"  name='name' id="one" placeholder="名字">
                <label for="one">姓名</label>
            </div>
            <div class="form-floating mb-4">
                <input type="text" class="form-control" id="five" name="nickname" placeholder="昵称">
                <label for="five">昵称</label>
            </div>
            <div class="form-floating mb-4">
                <input type="number" class="form-control" id="two" name="age" placeholder="年龄">
                <label for="two">年龄</label>
            </div>
            <div class="form-floating mb-4">
                <select class="form-select" name="gender" id="floatingSelect">
{{--                    <option selected value="1">Gender</option>--}}
                    <option value="1">男</option>
                    <option value="2">女</option>
{{--                    <option value="3">other</option>--}}
                </select>
                <label for="floatingSelect">年龄</label>
            </div>
        </form>
    </section>
    <div class="divider"></div>
    <section class="detail-form-section pt-0 px-15">
        <form>
            <div class="form-floating mb-4">
                <input type="number" class="form-control" id="six"  name='phone' value="9876543210" placeholder="Mobile Number">
                <label for="six">手机号</label>
{{--                <a class="change-btn" href="#">change</a>--}}
            </div>
{{--            <div class="form-floating mb-4">--}}
{{--                <input type="password" class="form-control" id="eight" value="545454" placeholder="Password">--}}
{{--                <label for="eight">Password</label>--}}
{{--                <a class="change-btn" href="#">change</a>--}}
{{--            </div>--}}
        </form>
    </section>
    <!-- detail form end -->


    <!-- panel space start -->
    <section class="panel-space"></section>
    <!-- panel space end -->


    <!-- bottom panel start -->
    <div class="cart-bottom row m-0">
        <div>
            <div class="left-content col-5">
                <a href="javascript:location.reload()" class="title-color">重新输入</a>
            </div>
            <a href="delivery.html" class="btn btn-solid col-7 text-uppercase">保存信息</a>
        </div>
    </div>
    <!-- bottom panel end -->


    <!-- latest jquery-->
    <script src="{{URL::asset('assets1/js/jquery-3.3.1.min.js')}}"></script>

    <!-- Bootstrap js-->
    <script src="{{URL::asset('assets1/js/bootstrap.bundle.min.js')}}"></script>

    <!-- Slick js-->
    <script src="{{URL::asset('assets1/js/slick.js')}}"></script>

    <!-- script js -->
    <script src="{{URL::asset('assets1/js/script.js')}}"></script>
    <script type="text/javascript">
{{--        读取上传照片--}}
        function photoupload(){
            var file = document.getElementById("photo").files[0];
            if(file.size>5*1024*1024){
                alert('图片大小不能超过3M');
                return false;
            }

            var reader = new FileReader();
            reader.onload = function (e) {
                console.log("成功读取....");

                var img = document.getElementById("selfie");

                img.src = e.target.result;
            }
            reader.readAsDataURL(file)



            {{--var form = new FormData();--}}
            {{--form.append("img", img);--}}
            {{--console.log(form);--}}

            {{--$.ajax({--}}
            {{--    type: 'post',--}}
            {{--    url: "{{url('/Photo')}}",--}}
            {{--    data: form,--}}
            {{--    processData: false,--}}
            {{--    contentType : false,--}}
            {{--    headers: {--}}
            {{--        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')--}}
            {{--    },--}}
            {{--    success: function (data,status) {--}}
            {{--        if(data==false){--}}
            {{--            alert('图片错误');--}}
            {{--        }else{--}}
            {{--            $('#selfie').attr('src',"{{url('')}}"+"/"+data)--}}
            {{--        }--}}
            {{--    },--}}
            {{--    error:function(xhr, type, errorthrown){--}}
            {{--        if(xhr.status==419){--}}
            {{--            alert('恶意请求');--}}
            {{--        }else if(xhr.status==500){--}}
            {{--            alert('服务器有问题请联系管理员');--}}
            {{--        }--}}
            {{--    }--}}
            {{--});--}}
        }
    </script>

</body>

</html>