<br><br><br>
    <div class="container text-center" style="margin: 2em auto;">
    <h2 class="tex-center">Tabel Leadframe Keluar</h2>
    <a href="<?=base_url('report/barangKeluarManual')?>" style="margin-bottom:10px;float:left;" type="button" class="btn btn-danger" name="laporan_data"><i class="fa fa-file-text" aria-hidden="true"></i> Invoice Manual</a>
    <div class="tabel" style="margin-top:80px">
    <table class="table table-bordered table-striped" style="margin: 2em auto;" id="tabel_leadframekeluar">
    <thead>
      <tr>
        <th>No</th>
        <th>ID_Transaksi</th>
        <th>Supplier</th>
        <th>Part No</th>
        <th>Lot Number</th>
        <th>Expired Date</th>
        <th>Quantity</th>
        <th>Remarks</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <?php if(is_array($list_data)){ ?>
        <?php $no = 1;?>
        <?php foreach($list_data as $dd): ?>
          <td><?=$no?></td>
          <td><?=$dd->id_transaksi?></td>
          <td><?=$dd->supplier?></td>
          <td><?=$dd->partno?></td>
          <td><?=$dd->lotnumber?></td>
          <td><?=$dd->expdate?></td>
          <td><?=$dd->jumlah?></td>
          <td><?=$dd->remarks?></td>
    <?php $no++; ?>
    <?php endforeach;?>
    <?php }else { ?>
          <td colspan="7" align="center"><strong>Data Kosong</strong></td>
    <?php } ?>
    </tbody>
  </table>
  </div>
</div>

<script type="text/javascript">
  $(document).ready(function(){
    $('#tabel_leadframekeluar').DataTable();
  });
</script>
