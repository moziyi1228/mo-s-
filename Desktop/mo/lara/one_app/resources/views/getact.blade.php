@extends('layouts.app')
    @section('content')
           <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <h1 class="h3 mb-2 text-gray-800">ONE仔活动列表</h1>
                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary"></h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>活动名称</th>
                                            <th>活动开始时间</th>
                                            <th>活动结束时间</th>
                                            <th>活动人数</th>
                                            <th>活动收费</th>
                                            <th>创建时间</th>
                                            <th>操作</th>
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
                                    <tbody>
                                    @for ($i = 0; $i < count($actinfo); $i++)
                                        <tr>
                                            <td>{{$actinfo[$i]->actname}}</td>
                                            <td>{{date('Y-m-d H:i',strtotime($actinfo[$i]->starttime))}}</td>
                                            <td>{{date('Y-m-d H:i',strtotime($actinfo[$i]->endtime))}}</td>
                                            <td>{{$actinfo[$i]->enrollment}}</td>
                                            <td>{{$actinfo[$i]->charge}}</td>
                                            <td>{{date('Y-m-d H:i',strtotime($actinfo[$i]->time))}}</td>
                                            <td>
                                                <a href="/delact/{{$actinfo[$i]->actid}} ">删除</a>
                                            </td>
                                        </tr>
                                    @endfor
                                    </tbody>
                                </table>
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
                        <span>Copyright &copy; Your Website 2020</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->


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
@endsection