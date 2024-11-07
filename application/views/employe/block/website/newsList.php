<div class="container-fluid">
    {system_message}
    {system_info}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <div class="row">
                <div class="col-sm-6">
                    <h4><b>News</b></h4>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{site_url}employe/website/addNews" class="btn btn-primary">Add News</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="example" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th width="85%">News</th>
                            <th width="10%">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                    $i = 1;
                    foreach($newsList as $list){
                    ?>

                        <tr>
                            <td width="5%"><?php echo $i; ?></td>
                            <td width="85%"><?php echo $list['news']; ?></td>
                            <td width="10%"><a href="{site_url}employe/website/editNews/<?php echo $list['id']; ?>"
                                    class="btn btn-primary btn-sm"><i class="fa fa-edit"></i></a>
                                <a href="{site_url}employe/website/deleteNews/<?php echo $list['id']; ?>"
                                    class="btn btn-danger btn-sm"
                                    onclick="return confirm('Are you sure want to delete?')"><i
                                        class="fa fa-trash"></i></a>
                            </td>
                        </tr>

                        <?php $i++;} ?>
                    </tbody>

                    <tfoot>
                        <tr>
                            <th>#</th>
                            <th width="50%">News</th>
                            <th>Action</th>
                        </tr>
                    </tfoot>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</div>