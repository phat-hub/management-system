<?php $this->layout("layouts/default", ["title" => APPNAME]) ?>

<?php $this->start("page") ?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="text-center py-3 px-1">
                <h1>Không tìm thấy trang</h1>
                <div class="text-muted my-3">
                    Rất tiếc, lỗi đã xảy ra.
                    Trang mà bạn yêu cầu không thể tìm thấy.
                </div>
                <div class="my-1">
                    <a href="/" class="btn btn-primary btn-lg me-1">
                        <i class="fa fa-home"></i> Trở về trang chủ</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->stop() ?>