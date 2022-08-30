@extends('layouts.app')

@section('content')

    <script src="vendor/jquery/jquery.min.js"></script>
    <script>
        function readAsDataURL(){
            //检验是否为图像文件
            var file = document.getElementById("img").files[0];
            console.log(file)
            if(!/image\/\w+/.test(file.type)){
                alert("看清楚，这个需要图片！");

                return false;
            }else{
                var reader = new FileReader();
                //将文件以Data URL形式读入页面
                reader.readAsDataURL(file);
                reader.onload=function(e){
                    var result=document.getElementById("image");
                    //显示文件
                    result.src= this.result ;
                }
            }
        }

    </script>
    <script>

        function createAct(){

            var actname=$('#actname').val();
            var starttime=$('#starttime').val();
            var endtime=$('#endtime').val();
            var url=$('#url').val();
            var enrollment=$('#enrollment').val();
            var charge=$('#charge').val();
            var img=$('#img').val();
            var address=$('#address').val()


            if(!actname){
                $('#actname').attr("placeholder","活动名不能为空");
            }else if(!starttime){
               alert("活动开始时间不能为空");
            }else if(!endtime){
                alert("活动结束时间不能为空");
            }else if(!url){
                $('#url').attr("placeholder","链接不能为空");
            }else if(!enrollment){
                $('#enrollment').attr("placeholder","活动人数不能为空");
            }else if(!charge){
                $('#charge').attr("placeholder","报名费不能为空");
            }else if(!address){
                $('#address').attr('placeholder',"活动地址不能为空");
            }else {
                var formData = new FormData($('#form')[0]);


                console.log(formData);
                $.ajax({
                    type: 'post',
                    url: "{{url('/api/createact')}}",
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                    success: function (data, status) {
                        return 0;
                        if (data == 1) {
                            alert('创建成功');
                            window.location.replace("/actlist");
                        } else {
                            alert('创建失败');
                            window.location.replace("/index/");
                        }
                    },
                    error: function (xhr, type, errorthrown) {
                        if (xhr.status == 419) {
                            alert('恶意请求');
                        } else if (xhr.status == 500) {
                            alert('服务器有问题请联系管理员');
                        }
                    }
                });
            }
        };

    </script>
            <!-- Main Content -->
            <div id="content" xmlns="http://www.w3.org/1999/html">
                <!-- Begin Page Content -->
                <div class="container-fluid">
                    <!-- Page Heading -->
                    <h1 class="h3 mb-2 text-gray-800">创建活动</h1>
                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <form id="form" >
                                    <table class="table table-bordered" id="dataTable" width="90%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>活动名称</th><td><input id="actname" type="text" name="actname" ></td>
                                            </tr>
                                        </thead>
                                        {{--<tfoot>--}}
                                            {{--<tr>--}}
                                                {{--<th>Name</th>--}}
                                                {{--<th>Position</th>--}}
                                                {{--<th>Office</th>--}}
                                                {{--<th>Age</th>--}}
                                                {{--<th>Start date</th>--}}
                                                {{--<th>Salary</th>--}}
                                            {{--</tr>--}}
                                        {{--</tfoot>--}}
                                        <tr>
                                            <tr>
                                                <th>活动开始时间</th>
                                                <td><input type="datetime-local" id="starttime" width="10px" name="starttime"></td>
                                            </tr>
                                            <tr>
                                                <th>活动结束时间</th>
                                                <td><input type="datetime-local" id="endtime" name="endtime"></td>  </tr>
                                            <tr>
                                                 <th>活动人数</th>
                                                <td><input type="text" id="enrollment" name="enrollment"></td>
                                            </tr>
                                            <tr>
                                                 <th>活动城市</th>
                                                <td>
                                                    <select  name="city">
                                                        <option value="广州">广州</option>
                                                        <option value="佛山">佛山</option>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>活动具体地址</th>
                                                <td><input type="text" id="address" name="address"></td>
                                            </tr>
                                            <tr>
                                                <th>活动收费</th>
                                                <td><input type="text" id="charge" name="charge"></td>
                                            </tr>
                                            <tr>
                                                <th>活动详情</th>
                                                <td>
                                                    <textarea id="detail" name="detail"></textarea>
                                                </td>
                                            </tr>
                                        <tr>
                                            <th>活动链接</th>
                                            <td><input type="text" id="url" name="url"></td>
                                        </tr>
                                        <tr>
                                            <th>活动展示图</th>
                                            <td><input type="file" id="img" name="img" onchange="readAsDataURL()">

                                                    <img alt="" style="width: 200px ;height: 80px;" id="image"/>

                                            </td>

                                        </tr>
                                            <tr>
                                                <th>操作</th>
                                                <td><button  type="button"  onclick="createAct()">提交</button></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        {{--<span>Copyright &copy; Your Website 2020</span>--}}
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->
    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="login.html">Logout</a>
                </div>
            </div>
        </div>
    </div>
            <script>
                // function setdisplay(){
                //     var filter = document.getElementById('dataTable_filter');
                //     filter.style.display='none';
                //     document.getElementById('dataTable_length').style.display='none';
                //     document.getElementById('dataTable_info').style.display='none';
                //     document.getElementById('dataTable_paginate').style.display='none';
                // }
                // window.onload=function() {
                //     setdisplay();
                // }

            </script>


@endsection


