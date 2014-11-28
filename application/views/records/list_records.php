<div class="row">
  <div class="col-sm-12 col-md-12 col-lg-12">
    <table class="table table-striped table-bordered data-table">
      <thead>
        <tr>
          <?php foreach($columns as $col){ ?>
          <th><?php echo $col['header'] ?></th>
          <?php } ?>
        </tr>
      </thead>
      <!--ajax processing puts data in -->
      <tfoot>
        <tr>
          <?php foreach($columns as $col){ ?>
          <th><?php echo $col['header'] ?></th>
          <?php } ?>
        </tr>
      </tfoot>
    </table>
  </div>
</div>


