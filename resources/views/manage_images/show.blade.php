@extends('manage_images.layout')
<body>
    <div class="container mt-5">

        <form action="./manager_images_remove_last" method="get">
            <button name="foo" value="upvote">Xóa ảnh không truy cập</button>
        </form>

        <form action="./manager_images" method="get">
            <button name="foo" value="upvote">Tất cả</button>
        </form>
        
        <div class="row">
            <div class="col-3">
                Tổng: {{$total_all}}
            </div>
            <div class="col-3">
                Tổng không truy cập 3 tháng: {{$total_no_access_3_month}}
            </div>
            <div class="col-3">
                Không thể tìm ảnh xóa: {{$total_not_found ?? 0}}
            </div>
            <div class="col-3">
               Đã xóa ảnh cũ: {{$remove_ok ?? 0}}
            </div>


        </div>

        <table class="table table-bordered mb-5">
            <thead>
                <tr class="table-success">
                    <th scope="col">#</th>
                    <th scope="col">Tên ảnh</th>
                    <th scope="col">Đường dẫn</th>
                    <th scope="col">Truy cập lần cuối</th>
                    <th scope="col">Ngày up</th>
                    <th scope="col">Truy cập cách đây</th>

                </tr>
            </thead>
            <tbody>
                @foreach($images as $data)
                <tr>
                    <th scope="row">{{ $data->id }}</th>
                    <td>{{ $data->title }}</td>
                    <td><a href="./api/SHImages/{{ $data->title}}" class="link-primary">{{ $data->path }}</a></td>

                       <td><img width="100" height="100" src="./api/SHImages/{{ $data->title}}" /></td>
                    <td>{{ $data->time_access ?? ""}}</td>
                    <td>{{ $data->created_at->format('H:m:s d-m-Y') }}</td>
                    <td>{{ $data->getDiffTimeAccess() }}</td>
                    
               
                </tr>
                @endforeach
            </tbody>
        </table>
        {{-- Pagination --}}
      
        <div class="d-flex">
            <div class="mx-auto">
                {{$images->links("pagination::bootstrap-4")}}
            </div>
        </div>
    </div>
</body>
</html>