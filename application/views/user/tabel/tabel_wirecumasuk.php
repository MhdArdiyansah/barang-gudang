<br><br><br>
    <div class="container text-center" style="margin: 2em auto;">
    <h2 class="tex-center">Tabel Wire CU Masuk</h2>
    <table class="table table-bordered table-striped" style="margin: 2em auto;" id="tabel_wirecumasuk">
    <thead>
      <tr>
      <th>No</th>
        <th>ID_Transaksi</th>
        <th>Supplier</th>
        <th>Wire CU Type</th>
        <th>Part No</th>
        <th>Lot Number</th>
        <th>Expired Date</th>
        <th>Spoll</th>
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
          <td><?=$dd->wirecutype?></td>
          <td><?=$dd->partno?></td>
          <td><?=$dd->lotnumber?></td>
          <td><?=$dd->expdate?></td>
          <td><?=$dd->jumlah?></td>
          <td><?=$dd->remarks?></td>
      </tr>
    <?php $no++; ?>
    <?php endforeach;?>
    <?php }else { ?>
          <td colspan="7" align="center"><strong>Data Kosong</strong></td>
    <?php } ?>
    </tbody>
  </table>
  </div>

<script type="text/javascript">
  $(document).ready(function(){
    $('#tabel_wirecumasuk').DataTable();
  });
</script>
