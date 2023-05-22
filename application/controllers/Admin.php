<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller{

  public function __construct(){
		parent::__construct();
    $this->load->model('M_admin');
    $this->load->library('upload');
	}

  public function index(){
    if($this->session->userdata('status') == 'login' && $this->session->userdata('role') == 1){
      $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
      $data['stokBarangMasuk'] = $this->M_admin->sum('tb_barang_masuk','jumlah');
      $data['stokMaterialMasukCompoud'] = $this->M_admin->sum('tb_compound_masuk','jumlah');
      $data['stokMaterialMasukLeadframe'] = $this->M_admin->sum('tb_leadframe_masuk','jumlah');
      $data['stokMaterialMasukGlue'] = $this->M_admin->sum('tb_glue_masuk','jumlah');
      $data['stokMaterialMasukWafer'] = $this->M_admin->sum('tb_wafer_masuk','jumlah');
      $data['stokMaterialMasukSolder'] = $this->M_admin->sum('tb_solder_masuk','jumlah');
      $data['stokMaterialMasukWireau'] = $this->M_admin->sum('tb_wireau_masuk','jumlah');
      $data['stokMaterialMasukWirecu'] = $this->M_admin->sum('tb_wirecu_masuk','jumlah');
      $data['stokMaterialMasukWirealu'] = $this->M_admin->sum('tb_wirealu_masuk','jumlah');
      $data['stokBarangKeluar'] = $this->M_admin->sum('tb_barang_keluar','jumlah');
      $data['stokCompoundKeluar'] = $this->M_admin->sum('tb_compound_keluar','jumlah');   
      $data['stokLeadframeKeluar'] = $this->M_admin->sum('tb_leadframe_keluar','jumlah');
      $data['stokGlueKeluar'] = $this->M_admin->sum('tb_glue_keluar','jumlah');
      $data['stokWaferKeluar'] = $this->M_admin->sum('tb_wafer_keluar','jumlah');
      $data['stokSolderKeluar'] = $this->M_admin->sum('tb_solder_keluar','jumlah');
      $data['stokWireAuKeluar'] = $this->M_admin->sum('tb_wireau_keluar','jumlah');
      $data['stokWireCuKeluar'] = $this->M_admin->sum('tb_wirecu_keluar','jumlah');
      $data['stokWireAluKeluar'] = $this->M_admin->sum('tb_wirealu_keluar','jumlah');
      $data['dataUser'] = $this->M_admin->numrows('user');
      $this->load->view('admin/index',$data);
    }else {
      $this->load->view('login/login');
    }
  }

  public function sigout(){
    session_destroy();
    redirect('login');
  }

  ####################################
              // Profile
  ####################################

  public function profile()
  {
    $data['token_generate'] = $this->token_generate();
    $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->session->set_userdata($data);
    $this->load->view('admin/profile',$data);
  }

  public function token_generate()
  {
    return $tokens = md5(uniqid(rand(), true));
  }

  private function hash_password($password)
  {
    return password_hash($password,PASSWORD_DEFAULT);
  }

  public function proses_new_password()
  {
    $this->form_validation->set_rules('email','Email','required');
    $this->form_validation->set_rules('new_password','New Password','required');
    $this->form_validation->set_rules('confirm_new_password','Confirm New Password','required|matches[new_password]');

    if($this->form_validation->run() == TRUE)
    {
      if($this->session->userdata('token_generate') === $this->input->post('token'))
      {
        $username = $this->input->post('username');
        $email = $this->input->post('email');
        $new_password = $this->input->post('new_password');

        $data = array(
            'email'    => $email,
            'password' => $this->hash_password($new_password)
        );

        $where = array(
            'id' =>$this->session->userdata('id')
        );

        $this->M_admin->update_password('user',$where,$data);

        $this->session->set_flashdata('msg_berhasil','Password Telah Diganti');
        redirect(base_url('admin/profile'));
      }
    }else {
      $this->load->view('admin/profile');
    }
  }

  public function proses_gambar_upload()
  {
    $config =  array(
                   'upload_path'     => "./assets/upload/user/img/",
                   'allowed_types'   => "gif|jpg|png|jpeg",
                   'encrypt_name'    => False, //
                   'max_size'        => "50000",  // ukuran file gambar
                   'max_height'      => "9680",
                   'max_width'       => "9024"
                 );
      $this->load->library('upload',$config);
      $this->upload->initialize($config);

      if( ! $this->upload->do_upload('userpicture'))
      {
        

        $this->session->set_flashdata('msg_error_gambar', $this->upload->display_errors());
        $this->load->view('admin/profile',$data);
      }else{
        

        $upload_data = $this->upload->data();
        $nama_file = $upload_data['file_name'];
        $ukuran_file = $upload_data['file_size'];

        //resize img + thumb Img -- Optional
        $config['image_library']     = 'gd2';
				$config['source_image']      = $upload_data['full_path'];
				$config['create_thumb']      = FALSE;
				$config['maintain_ratio']    = TRUE;
				$config['width']             = 150;
				$config['height']            = 150;

        $this->load->library('image_lib', $config);
        $this->image_lib->initialize($config);
				if (!$this->image_lib->resize())
        {
          
          $data['pesan_error'] = $this->image_lib->display_errors();
          // var_dump($data);die;
          $this->load->view('admin/profile',$data);
        }

        $where = array(
                'username_user' => $this->session->userdata('name')
        );

        $data = array(
                'nama_file' => $nama_file,
                'ukuran_file' => $ukuran_file
        );
        // var_dump($where,$data);die;
        $this->M_admin->update('tb_upload_gambar_user',$data,$where);
        $this->session->set_flashdata('msg_berhasil_gambar','Gambar Berhasil Di Upload');
        redirect(base_url('admin/profile'));
      }
  }

  ####################################
           // End Profile
  ####################################



  ####################################
              // Users
  ####################################
  public function users()
  {
    $data['list_users'] = $this->M_admin->kecuali('user',$this->session->userdata('name'));
    $data['token_generate'] = $this->token_generate();
    $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->session->set_userdata($data);
    $this->load->view('admin/users',$data);
  }

  public function form_user()
  {
    $data['list_satuan'] = $this->M_admin->select('tb_satuan');
    $data['token_generate'] = $this->token_generate();
    $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->session->set_userdata($data);
    $this->load->view('admin/form_users/form_insert',$data);
  }

  public function update_user()
  {
    $id = $this->uri->segment(3);
    $where = array('id' => $id);
    $data['token_generate'] = $this->token_generate();
    $data['list_data'] = $this->M_admin->get_data('user',$where);
    $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->session->set_userdata($data);
    $this->load->view('admin/form_users/form_update',$data);
  }

  public function proses_delete_user()
  {
    $id = $this->uri->segment(3);
    $where = array('id' => $id);
    $this->M_admin->delete('user',$where);
    $this->session->set_flashdata('msg_berhasil','User Behasil Di Delete');
    redirect(base_url('admin/users'));

  }

  public function proses_tambah_user()
  {
    $this->form_validation->set_rules('username','Username','required');
    $this->form_validation->set_rules('email','Email','required|valid_email');
    $this->form_validation->set_rules('password','Password','required');
    $this->form_validation->set_rules('confirm_password','Confirm password','required|matches[password]');

    if($this->form_validation->run() == TRUE)
    {
      if($this->session->userdata('token_generate') === $this->input->post('token'))
      {

        $username     = $this->input->post('username',TRUE);
        $email        = $this->input->post('email',TRUE);
        $password     = $this->input->post('password',TRUE);
        $role         = $this->input->post('role',TRUE);

        $data = array(
              'username'     => $username,
              'email'        => $email,
              'password'     => $this->hash_password($password),
              'role'         => $role,
        );
        $data2 = array(
          'username_user'     => $username,
          'nama_file  '        => '-',
          'ukuran_file'         => 0,
    );
        $this->M_admin->insert('user',$data);
        $this->M_admin->insert('tb_upload_gambar_user',$data2);

        $this->session->set_flashdata('msg_berhasil','User Berhasil Ditambahkan');
        redirect(base_url('admin/form_user'));
        }
      }else {
        $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
        $this->load->view('admin/form_users/form_insert',$data);
    }
  }

  public function proses_update_user()
  {
    $this->form_validation->set_rules('username','Username','required');
    $this->form_validation->set_rules('email','Email','required|valid_email');

    
    if($this->form_validation->run() == TRUE)
    {
      if($this->session->userdata('token_generate') === $this->input->post('token'))
      {
        $id           = $this->input->post('id',TRUE);        
        $username     = $this->input->post('username',TRUE);
        $email        = $this->input->post('email',TRUE);
        $role         = $this->input->post('role',TRUE);

        $where = array('id' => $id);
        $data = array(
              'username'     => $username,
              'email'        => $email,
              'role '        => $role,
        );
        $this->M_admin->update('user',$data,$where);
        $this->session->set_flashdata('msg_berhasil','Data User Berhasil Diupdate');
        redirect(base_url('admin/users'));
       }
    }else{
        $this->load->view('admin/form_users/form_update');
    }
  }


  ####################################
           // End Users
  ####################################



  ####################################
        // DATA BARANG MASUK
  ####################################

  public function form_barangmasuk()
  {
    $data['list_satuan'] = $this->M_admin->select('tb_satuan');
    $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->load->view('admin/form_barangmasuk/form_insert',$data);
  }

  public function tabel_barangmasuk()
  {
    $data = array(
              'list_data' => $this->M_admin->select('tb_barang_masuk'),
              'avatar'    => $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'))
            );
    $this->load->view('admin/tabel/tabel_barangmasuk',$data);
  }

  public function update_barang($id_transaksi)
  {
    $where = array('id_transaksi' => $id_transaksi);
    $data['data_barang_update'] = $this->M_admin->get_data('tb_barang_masuk',$where);
    $data['list_satuan'] = $this->M_admin->select('tb_satuan');
    $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->load->view('admin/form_barangmasuk/form_update',$data);
  }

  public function delete_barang($id_transaksi)
  {
    $where = array('id_transaksi' => $id_transaksi);
    $this->M_admin->delete('tb_barang_masuk',$where);
    redirect(base_url('admin/tabel_barangmasuk'));
  }


  public function proses_databarang_masuk_insert()
  {
    $this->form_validation->set_rules('lokasi','Lokasi','required');
    $this->form_validation->set_rules('kode_barang','Kode Barang','required');
    $this->form_validation->set_rules('nama_barang','Nama Barang','required');
    $this->form_validation->set_rules('jumlah','Jumlah','required');

    if($this->form_validation->run() == TRUE)
    {
      $id_transaksi = $this->input->post('id_transaksi',TRUE);
      $tanggal      = $this->input->post('tanggal',TRUE);
      $lokasi       = $this->input->post('lokasi',TRUE);
      $kode_barang  = $this->input->post('kode_barang',TRUE);
      $nama_barang  = $this->input->post('nama_barang',TRUE);
      $satuan       = $this->input->post('satuan',TRUE);
      $jumlah       = $this->input->post('jumlah',TRUE);

      $data = array(
            'id_transaksi' => $id_transaksi,
            'tanggal'      => $tanggal,
            'lokasi'       => $lokasi,
            'kode_barang'  => $kode_barang,
            'nama_barang'  => $nama_barang,
            'satuan'       => $satuan,
            'jumlah'       => $jumlah
      );
      $this->M_admin->insert('tb_barang_masuk',$data);

      $this->session->set_flashdata('msg_berhasil','Data Barang Berhasil Ditambahkan');
      redirect(base_url('admin/form_barangmasuk'));
    }else {
      $data['list_satuan'] = $this->M_admin->select('tb_satuan');
      $this->load->view('admin/form_barangmasuk/form_insert',$data);
    }
  }

  public function proses_databarang_masuk_update()
  {
    $this->form_validation->set_rules('lokasi','Lokasi','required');
    $this->form_validation->set_rules('kode_barang','Kode Barang','required');
    $this->form_validation->set_rules('nama_barang','Nama Barang','required');
    $this->form_validation->set_rules('jumlah','Jumlah','required');

    if($this->form_validation->run() == TRUE)
    {
      $id_transaksi = $this->input->post('id_transaksi',TRUE);
      $tanggal      = $this->input->post('tanggal',TRUE);
      $lokasi       = $this->input->post('lokasi',TRUE);
      $kode_barang  = $this->input->post('kode_barang',TRUE);
      $nama_barang  = $this->input->post('nama_barang',TRUE);
      $satuan       = $this->input->post('satuan',TRUE);
      $jumlah       = $this->input->post('jumlah',TRUE);

      $where = array('id_transaksi' => $id_transaksi);
      $data = array(
            'id_transaksi' => $id_transaksi,
            'tanggal'      => $tanggal,
            'lokasi'       => $lokasi,
            'kode_barang'  => $kode_barang,
            'nama_barang'  => $nama_barang,
            'satuan'       => $satuan,
            'jumlah'       => $jumlah
      );
      $this->M_admin->update('tb_barang_masuk',$data,$where);
      $this->session->set_flashdata('msg_berhasil','Data Barang Berhasil Diupdate');
      redirect(base_url('admin/tabel_barangmasuk'));
    }else{
      $this->load->view('admin/form_barangmasuk/form_update');
    }
  }

  
  public function form_compoundmasuk()
  {
    $data['list_satuan'] = $this->M_admin->select('tb_satuan');
    $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->load->view('admin/form_compoundmasuk/form_insert_compound',$data);
  }

  public function tabel_compoundmasuk()
  {
    $data = array(
              'list_data' => $this->M_admin->select('tb_compound_masuk'),
              'avatar'    => $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'))
            );
    $this->load->view('admin/tabel/tabel_compoundmasuk',$data);
  }

  public function update_compound($id_transaksi)
  {
    $where = array('id_transaksi' => $id_transaksi);
    // var_dump(1);die;
    $data['data_compound_update'] = $this->M_admin->get_data('tb_compound_masuk',$where);
    $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->load->view('admin/form_compoundmasuk/form_update_compound',$data);
  }

  public function delete_compound($id_transaksi)
  {
    $where = array('id_transaksi' => $id_transaksi);
    $this->M_admin->delete('tb_compound_masuk',$where);
    redirect(base_url('admin/tabel_compoundmasuk'));
  }


  public function proses_datacompound_masuk_insert()
  {
    
    $this->form_validation->set_rules('lotnumber','Lotnumber','required');
    $this->form_validation->set_rules('partno','Partno','required');
    $this->form_validation->set_rules('jumlah','Jumlah','required');

    if($this->form_validation->run() == TRUE)
    {
      $id_transaksi = $this->input->post('id_transaksi',TRUE);
      $supplier  = $this->input->post('supplier',TRUE);
      $partno       = $this->input->post('partno',TRUE);
      $lotnumber    = $this->input->post('lotnumber',TRUE);
      $expdate      = $this->input->post('expdate',TRUE);
      $jumlah       = $this->input->post('jumlah',TRUE);
      $remarks      = $this->input->post('remarks',TRUE);

      $data = array(
            'id_transaksi' =>$id_transaksi,
            'supplier'     => $supplier,     
            'partno'       => $partno,
            'lotnumber'    => $lotnumber,
            'expdate'      => $expdate,
            'jumlah'       => $jumlah,
            'remarks'      => $remarks
      );
      $this->M_admin->insert('tb_compound_masuk',$data);

      $this->session->set_flashdata('msg_berhasil','Data Barang Berhasil Ditambahkan');
      redirect(base_url('admin/form_compoundmasuk'));
    }else {
      $data['list_satuan'] = $this->M_admin->select('tb_satuan');
      $this->load->view('admin/form_compoundmasuk/form_insert_compound',$data);
    }
  }

  public function proses_datacompound_masuk_update()
  {
    
    $this->form_validation->set_rules('partno','Partno','required');
    $this->form_validation->set_rules('lotnumber','Lotnumber','required');
    $this->form_validation->set_rules('jumlah','Jumlah','required');

    if($this->form_validation->run() == TRUE)
    { 
      $id_transaksi = $this->input->post('id_transaksi',TRUE);
      $supplier     = $this->input->post('supplier',TRUE);
      $partno       = $this->input->post('partno',TRUE);
      $lotnumber    = $this->input->post('lotnumber',TRUE);
      $expdate      = $this->input->post('expdate',TRUE);
      $jumlah       = $this->input->post('jumlah',TRUE);
      $remarks      = $this->input->post('remarks',TRUE);
 

      $where = array('id_transaksi' => $id_transaksi);
      $data = array(
            'id_transaksi' => $id_transaksi,
            'supplier'     => $supplier,
            'partno'       => $partno,
            'lotnumber'    => $lotnumber,
            'expdate'      => $expdate,
            'jumlah'       => $jumlah,
            'remarks'      => $remarks, 
      );
      $this->M_admin->update('tb_compound_masuk',$data,$where);
      $this->session->set_flashdata('msg_berhasil','Data Barang Berhasil Diupdate');
      redirect(base_url('admin/tabel_compoundmasuk'));  
    }else{
      $this->load->view('admin/form_compoundmasuk/form_ ');
    }
  }

  public function form_leadframemasuk()
  {
    $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->load->view('admin/form_leadframemasuk/form_insert_leadframe',$data);
  }

  public function tabel_leadframemasuk()
  {   
    $data = array(
              'list_data' => $this->M_admin->select('tb_leadframe_masuk'),
              'avatar'    => $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'))
            );
    $this->load->view('admin/tabel/tabel_leadframemasuk',$data);
  }

  public function update_leadframe($id_transaksi)
  {
    $where = array('id_transaksi' => $id_transaksi);
    $data['data_leadframe_update'] = $this->M_admin->get_data('tb_leadframe_masuk',$where);
    $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->load->view('admin/form_leadframemasuk/form_update_leadframe',$data);
  }

  public function delete_leadframe($id_transaksi)
  {
    $where = array('id_transaksi' => $id_transaksi);
    $this->M_admin->delete('tb_leadframe_masuk',$where);
    redirect(base_url('admin/tabel_leadframemasuk'));
  }

  public function proses_dataleadframe_masuk_insert()
  {
    $this->form_validation->set_rules('supplier','supplier','required');
    $this->form_validation->set_rules('lotnumber','lotnumber','required');
    $this->form_validation->set_rules('partno','partno','required');
    $this->form_validation->set_rules('jumlah','jumlah','required');

    if($this->form_validation->run() == TRUE)
    {
      $id_transaksi    = $this->input->post('id_transaksi',TRUE);
      $supplier        = $this->input->post('supplier',TRUE);
      $partno          = $this->input->post('partno',TRUE);
      $lotnumber       = $this->input->post('lotnumber',TRUE);
      $expdate         = $this->input->post('expdate',TRUE);
      $jumlah          = $this->input->post('jumlah',TRUE);
      $remarks         = $this->input->post('remarks',TRUE);

      $data = array(
            'id_transaksi'  => $id_transaksi,
            'supplier'      => $supplier,     
            'partno'        => $partno,
            'lotnumber'     => $lotnumber,
            'expdate'       => $expdate,
            'jumlah'        => $jumlah,
            'remarks'       => $remarks
      );
      $this->M_admin->insert('tb_leadframe_masuk',$data);

      $this->session->set_flashdata('msg_berhasil','Data Barang Berhasil Ditambahkan');
      redirect(base_url('admin/form_leadframemasuk'));
    }else {
      $data['list_satuan'] = $this->M_admin->select('tb_satuan');
      $this->load->view('admin/form_leadframemasuk/form_insert_leadframe',$data);
    }
  }

  public function proses_dataleadframe_masuk_update()
  {
    $this->form_validation->set_rules('supplier','supplier','required');
    $this->form_validation->set_rules('partno','partno','required');
    $this->form_validation->set_rules('lotnumber','lotnumber','required');
    $this->form_validation->set_rules('jumlah','jumlah','required');

    if($this->form_validation->run() == TRUE)
    {
      $id_transaksi   = $this->input->post('id_transaksi',TRUE);
      $supplier       = $this->input->post('supplier',TRUE);
      $partno         = $this->input->post('partno',TRUE);
      $lotnumber      = $this->input->post('lotnumber',TRUE);
      $expdate        = $this->input->post('expdate',TRUE);
      $jumlah         = $this->input->post('jumlah',TRUE);
      $remarks        = $this->input->post('remarks',TRUE);


      $where = array('id_transaksi' => $id_transaksi);
      // var_dump($where);die;
      $data = array(
            'id_transaksi'  => $id_transaksi,
            'supplier'      => $supplier,
            'partno'        => $partno,
            'lotnumber'     => $lotnumber,
            'expdate'       => $expdate,
            'jumlah'        => $jumlah,
            'remarks'       => $remarks, 
      );
      $this->M_admin->update('tb_leadframe_masuk',$data,$where);
      $this->session->set_flashdata('msg_berhasil','Data Material Berhasil Diupdate');
      redirect(base_url('admin/tabel_leadframemasuk'));
    }else{
      $this->load->view('admin/form_leadframemasuk/form_update_leadframe');
    }
  }

  public function form_gluemasuk()
  {
    $data['list_satuan'] = $this->M_admin->select('tb_satuan');
    $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->load->view('admin/form_gluemasuk/form_insert_glue',$data);
  }

  public function tabel_gluemasuk()
  {
    $data = array(
              'list_data' => $this->M_admin->select('tb_glue_masuk'),
              'avatar'    => $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'))
            );
    $this->load->view('admin/tabel/tabel_gluemasuk',$data);
  }

  public function update_glue($id_transaksi)
  {
    $where = array('id_transaksi' => $id_transaksi);
    $data['data_glue_update'] = $this->M_admin->get_data('tb_glue_masuk',$where);
    $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->load->view('admin/form_gluemasuk/form_update_glue',$data);
  }

  public function delete_glue($id_transaksi)
  {
    $where = array('id_transaksi' => $id_transaksi);
    $this->M_admin->delete('tb_glue_masuk',$where);
    redirect(base_url('admin/tabel_gluemasuk'));
  }


  public function proses_dataglue_masuk_insert()
  {
    $this->form_validation->set_rules('partno','Partno','required');
    $this->form_validation->set_rules('lotnumber','Lotnumber','required');
    $this->form_validation->set_rules('gluetype','Gluetype','required');
    $this->form_validation->set_rules('jumlah','Jumlah','required');

    if($this->form_validation->run() == TRUE)
    {
      $id_transaksi =$this->input->post('id_transaksi',TRUE);
      $supplier     = $this->input->post('supplier',TRUE);
      $gluetype     = $this->input->post('gluetype',TRUE);
      $partno       = $this->input->post('partno',TRUE);
      $lotnumber    = $this->input->post('lotnumber',TRUE);
      $mfgdate      = $this->input->post('mfgdate',TRUE);
      $expdate      = $this->input->post('expdate',TRUE);
      $jumlah       = $this->input->post('jumlah',TRUE);
      $balance      = $this->input->post('balance',TRUE);
      $remarks      = $this->input->post('remarks',TRUE);

      $data = array(
            'id_transaksi' => $id_transaksi,
            'supplier'     => $supplier,
            'gluetype'     => $gluetype,
            'partno'       => $partno,
            'lotnumber'    => $lotnumber,
            'expdate'      => $expdate,
            'jumlah'       => $jumlah,
            'remarks'      => $remarks
      );
      $this->M_admin->insert('tb_glue_masuk',$data);

      $this->session->set_flashdata('msg_berhasil','Data Barang Berhasil Ditambahkan');
      redirect(base_url('admin/form_gluemasuk'));
    }else {
      $data['list_satuan'] = $this->M_admin->select('tb_satuan');
      $this->load->view('admin/form_gluemasuk/form_insert_glue',$data);
    }
  }

  public function proses_dataglue_masuk_update()
  {
    $this->form_validation->set_rules('partno','Partno','required');
    $this->form_validation->set_rules('lotnumber','Lotnumber','required');
    $this->form_validation->set_rules('gluetype','gluetype','required');
    $this->form_validation->set_rules('jumlah','Jumlah','required');

    if($this->form_validation->run() == TRUE)
    {
      $id_transaksi  = $this->input->post('id_transaksi',TRUE);
      $supplier      = $this->input->post('supplier',TRUE);
      $gluetype      = $this->input->post('gluetype',TRUE);
      $partno        = $this->input->post('partno',TRUE);
      $lotnumber     = $this->input->post('lotnumber',TRUE);
      $expdate       = $this->input->post('expdate',TRUE);
      $jumlah        = $this->input->post('jumlah',TRUE);
      $remarks       = $this->input->post('remarks',TRUE);

      $where = array('id_transaksi' => $id_transaksi);
      $data = array(
            'id_transaksi' => $id_transaksi,
            'supplier'     => $supplier,
            'gluetype'     => $gluetype,
            'partno'       => $partno,
            'lotnumber'    => $lotnumber,
            'expdate'      => $expdate,
            'jumlah'       => $jumlah,
            'remarks'      => $remarks
      );
      $this->M_admin->update('tb_glue_masuk',$data,$where);
      $this->session->set_flashdata('msg_berhasil','Data Barang Berhasil Diupdate');
      redirect(base_url('admin/tabel_gluemasuk'));
    }else{
      $this->load->view('admin/form_gluemasuk/form_update_glue');
    }
  }

  public function form_wafermasuk()
  {
    $data['list_satuan'] = $this->M_admin->select('tb_satuan');
    $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->load->view('admin/form_wafermasuk/form_insert_wafer',$data);
  }

  public function tabel_wafermasuk()
  {
    $data = array(
              'list_data' => $this->M_admin->select('tb_wafer_masuk'),
              'avatar'    => $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'))
            );
    $this->load->view('admin/tabel/tabel_wafermasuk',$data);
  }

  public function update_wafer($id_transaksi)
  {
    $where = array('id_transaksi' => $id_transaksi);
    // var_dump(1);die;
    $data['data_wafer_update'] = $this->M_admin->get_data('tb_wafer_masuk',$where);
    $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->load->view('admin/form_wafermasuk/form_update_wafer',$data);
  }

  public function delete_wafer($id_transaksi)
  {
    $where = array('id_transaksi' => $id_transaksi);
    $this->M_admin->delete('tb_wafer_masuk',$where);
    redirect(base_url('admin/tabel_wafermasuk'));
  }


  public function proses_datawafer_masuk_insert()
  {
    $this->form_validation->set_rules('wafertype','Wafertype','required');
    $this->form_validation->set_rules('lotnumber','Lotnumber','required');
    $this->form_validation->set_rules('partno','Partno','required');
    $this->form_validation->set_rules('jumlah','Jumlah','required');

    if($this->form_validation->run() == TRUE)
    {
      $id_transaksi = $this->input->post('id_transaksi',TRUE);
      $supplier     = $this->input->post('supplier',TRUE);
      $wafertype    = $this->input->post('wafertype',TRUE);
      $pellet       = $this->input->post('pellet',TRUE);
      $partno       = $this->input->post('partno',TRUE);
      $lotnumber    = $this->input->post('lotnumber',TRUE);
      $expdate      = $this->input->post('expdate',TRUE);
      $jumlah       = $this->input->post('jumlah',TRUE);
      $remarks      = $this->input->post('remarks',TRUE);

      $data = array(
            'id_transaksi' => $id_transaksi,
            'supplier'     => $supplier,
            'wafertype'    => $wafertype,
            'pellet'       => $pellet,
            'partno'       => $partno,
            'lotnumber'    => $lotnumber,
            'expdate'      => $expdate,
            'jumlah'       => $jumlah,
            'remarks'      => $remarks
      );
      $this->M_admin->insert('tb_wafer_masuk',$data);

      $this->session->set_flashdata('msg_berhasil','Data Barang Berhasil Ditambahkan');
      redirect(base_url('admin/form_wafermasuk'));
    }else {
      $data['list_satuan'] = $this->M_admin->select('tb_satuan');
      $this->load->view('admin/form_wafermasuk/form_insert_wafer',$data);
    }
  }

  public function proses_datawafer_masuk_update()
  {
    $this->form_validation->set_rules('wafertype','Wafertype','required');
    $this->form_validation->set_rules('partno','Partno','required');
    $this->form_validation->set_rules('lotnumber','Lotnumber','required');
    $this->form_validation->set_rules('jumlah','Jumlah','required');

    if($this->form_validation->run() == TRUE)
    {
      $id_transaksi = $this->input->post('id_transaksi',TRUE);
      $supplier     = $this->input->post('supplier',TRUE);
      $wafertype    = $this->input->post('wafertype',TRUE);
      $pellet       = $this->input->post('pellet',TRUE);
      $partno       = $this->input->post('partno',TRUE);
      $lotnumber    = $this->input->post('lotnumber',TRUE);
      $expdate      = $this->input->post('expdate',TRUE);
      $jumlah       = $this->input->post('jumlah',TRUE);
      $remarks      = $this->input->post('remarks',TRUE);
 

      $where = array('id_transaksi' => $id_transaksi);
      $data = array(
            'id_transaksi' => $id_transaksi,
            'supplier'     => $supplier,
            'wafertype'    => $wafertype,
            'pellet'       => $pellet,
            'partno'       => $partno,
            'lotnumber'    => $lotnumber,
            'expdate'      => $expdate,
            'jumlah'       => $jumlah,
            'remarks'      => $remarks, 
      );
      $this->M_admin->update('tb_wafer_masuk',$data,$where);
      $this->session->set_flashdata('msg_berhasil','Data Barang Berhasil Diupdate');
      redirect(base_url('admin/tabel_wafermasuk'));  
    }else{
      $this->load->view('admin/form_wafermasuk/form_update_wafer');
    }
  }

  public function form_soldermasuk()
  {
    $data['list_satuan'] = $this->M_admin->select('tb_satuan');
    $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->load->view('admin/form_soldermasuk/form_insert_solderwire',$data);
  }

  public function tabel_solderwiremasuk()
  {
    $data = array(
              'list_data' => $this->M_admin->select('tb_solder_masuk'),
              'avatar'    => $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'))
            );
    $this->load->view('admin/tabel/tabel_solderwiremasuk',$data);
  }

  public function update_solder($id_transaksi)
  {
    $where = array('id_transaksi' => $id_transaksi);
    // var_dump(1);die;
    $data['data_solder_update'] = $this->M_admin->get_data('tb_solder_masuk',$where);
    $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->load->view('admin/form_soldermasuk/form_update_solderwire',$data);
  }

  public function delete_solder($id_transaksi)
  {
    $where = array('id_transaksi' => $id_transaksi);
    $this->M_admin->delete('tb_solder_masuk',$where);
    redirect(base_url('admin/tabel_solderwiremasuk'));
  }


  public function proses_datasolder_masuk_insert()
  {
    $this->form_validation->set_rules('soldertype','Soldertype','required');
    $this->form_validation->set_rules('lotnumber','Lotnumber','required');
    $this->form_validation->set_rules('partno','Partno','required');
    $this->form_validation->set_rules('jumlah','Jumlah','required');

    if($this->form_validation->run() == TRUE)
    {
      $id_transaksi = $this->input->post('id_transaksi',TRUE);
      $supplier     = $this->input->post('supplier',TRUE);
      $soldertype   = $this->input->post('soldertype',TRUE);
      $partno       = $this->input->post('partno',TRUE);
      $lotnumber    = $this->input->post('lotnumber',TRUE);
      $expdate      = $this->input->post('expdate',TRUE);
      $jumlah       = $this->input->post('jumlah',TRUE);
      $remarks      = $this->input->post('remarks',TRUE);

      $data = array(
            'id_transaksi' => $id_transaksi,
            'supplier'     => $supplier,     
            'soldertype'   => $soldertype,
            'partno'       => $partno,
            'lotnumber'    => $lotnumber,
            'expdate'      => $expdate,
            'jumlah'       => $jumlah,
            'remarks'      => $remarks
      );
      $this->M_admin->insert('tb_solder_masuk',$data);

      $this->session->set_flashdata('msg_berhasil','Data Barang Berhasil Ditambahkan');
      redirect(base_url('admin/form_soldermasuk'));
    }else {
      $data['list_satuan'] = $this->M_admin->select('tb_satuan');
      $this->load->view('admin/form_soldermasuk/form_insert_solderwire',$data);
    }
  }

  public function proses_datasolder_masuk_update()
  {
    $this->form_validation->set_rules('soldertype','Soldertype','required');
    $this->form_validation->set_rules('partno','Partno','required');
    $this->form_validation->set_rules('lotnumber','Lotnumber','required');
    $this->form_validation->set_rules('jumlah','Jumlah','required');

    if($this->form_validation->run() == TRUE)
    {
      $id_transaksi  = $this->input->post('id_transaksi',TRUE);
      $supplier      = $this->input->post('supplier',TRUE);
      $soldertype    = $this->input->post('soldertype',TRUE);
      $partno        = $this->input->post('partno',TRUE);
      $lotnumber     = $this->input->post('lotnumber',TRUE);
      $expdate       = $this->input->post('expdate',TRUE);
      $jumlah        = $this->input->post('jumlah',TRUE);
      $remarks       = $this->input->post('remarks',TRUE);
 

      $where = array('id_transaksi' => $id_transaksi);
      $data = array(
            'id_transaksi' => $id_transaksi,
            'supplier'     => $supplier,
            'soldertype'   => $soldertype,
            'partno'       => $partno,
            'lotnumber'    => $lotnumber,
            'expdate'      => $expdate,
            'jumlah'       => $jumlah,
            'remarks'      => $remarks, 
      );
      $this->M_admin->update('tb_solder_masuk',$data,$where);
      $this->session->set_flashdata('msg_berhasil','Data Barang Berhasil Diupdate');
      redirect(base_url('admin/tabel_solderwiremasuk'));  
    }else{
      $this->load->view('admin/form_soldermasuk/form_update_solderwire');
    }
  }

  public function form_wireaumasuk()
  {
    $data['list_satuan'] = $this->M_admin->select('tb_satuan');
    $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->load->view('admin/form_wireaumasuk/form_insert_wireau',$data);
  }

  public function tabel_wireaum()
  {
    $data = array(
              'list_data' => $this->M_admin->select('tb_wireau_masuk'),
              'avatar'    => $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'))
            );
    $this->load->view('admin/tabel/tabel_wireaum',$data);
  }

  public function update_wireau($id_transaksi)
  {
    $where = array('id_transaksi' => $id_transaksi);
    // var_dump(1);die;
    $data['data_wireau_update'] = $this->M_admin->get_data('tb_wireau_masuk',$where);
    $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->load->view('admin/form_wireaumasuk/form_update_wireau',$data);
  }

  public function delete_wireau($id_transaksi)
  {
    $where = array('id_transaksi' => $id_transaksi);
    $this->M_admin->delete('tb_wireau_masuk',$where);
    redirect(base_url('admin/tabel_wireaum'));
  }


  public function proses_datawireau_masuk_insert()
  {
    $this->form_validation->set_rules('wireautype','Wireautype','required');
    $this->form_validation->set_rules('lotnumber','Lotnumber','required');
    $this->form_validation->set_rules('partno','Partno','required');
    $this->form_validation->set_rules('jumlah','Jumlah','required');

    if($this->form_validation->run() == TRUE)
    {
      $id_transaksi  = $this->input->post('id_transaksi',TRUE);
      $supplier      = $this->input->post('supplier',TRUE);
      $wireautype    = $this->input->post('wireautype',TRUE);
      $partno        = $this->input->post('partno',TRUE);
      $lotnumber     = $this->input->post('lotnumber',TRUE);
      $expdate       = $this->input->post('expdate',TRUE);
      $jumlah        = $this->input->post('jumlah',TRUE);
      $remarks       = $this->input->post('remarks',TRUE);

      $data = array(
            'id_transaksi' => $id_transaksi,   
            'supplier'     => $supplier,
            'wireautype'   => $wireautype,
            'partno'       => $partno,
            'lotnumber'    => $lotnumber,
            'expdate'      => $expdate,
            'jumlah'       => $jumlah,
            'remarks'      => $remarks
      );
      $this->M_admin->insert('tb_wireau_masuk',$data);

      $this->session->set_flashdata('msg_berhasil','Data Barang Berhasil Ditambahkan');
      redirect(base_url('admin/form_wireaumasuk'));
    }else {
      $data['list_satuan'] = $this->M_admin->select('tb_satuan');
      $this->load->view('admin/form_wireaumasuk/form_insert_wireau',$data);
    }
  }

  public function proses_datawireau_masuk_update()
  {
    $this->form_validation->set_rules('wireautype','Wireautype','required');
    $this->form_validation->set_rules('partno','Partno','required');
    $this->form_validation->set_rules('lotnumber','Lotnumber','required');
    $this->form_validation->set_rules('jumlah','Jumlah','required');

    if($this->form_validation->run() == TRUE)
    {
      $id_transaksi  = $this->input->post('id_transaksi',TRUE);
      $supplier      = $this->input->post('supplier',TRUE);
      $wireautype    = $this->input->post('wireautype',TRUE);
      $partno        = $this->input->post('partno',TRUE);
      $lotnumber     = $this->input->post('lotnumber',TRUE);
      $expdate       = $this->input->post('expdate',TRUE);
      $jumlah        = $this->input->post('jumlah',TRUE);
      $remarks       = $this->input->post('remarks',TRUE);
 

      $where = array('id_transaksi' => $id_transaksi);
      $data = array(
            'id_transaksi'  => $id_transaksi,
            'supplier'      => $supplier,
            'wireautype'    => $wireautype,
            'partno'        => $partno,
            'lotnumber'     => $lotnumber,
            'expdate'       => $expdate,
            'jumlah'        => $jumlah,
            'remarks'       => $remarks, 
      );
      $this->M_admin->update('tb_wireau_masuk',$data,$where);
      $this->session->set_flashdata('msg_berhasil','Data Barang Berhasil Diupdate');
      redirect(base_url('admin/tabel_wireaum'));  
    }else{
      $this->load->view('admin/form_wireaumasuk/form_update_wireau');
    }
  }

  public function form_wirecumasuk()
  {
    $data['list_satuan'] = $this->M_admin->select('tb_satuan');
    $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->load->view('admin/form_wirecumasuk/form_insert_wirecu',$data);
  }

  public function tabel_wirecumasuk()
  {
    $data = array(
              'list_data' => $this->M_admin->select('tb_wirecu_masuk'),
              'avatar'    => $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'))
            );
    $this->load->view('admin/tabel/tabel_wirecumasuk',$data);
  }

  public function update_wirecu($id_transaksi)
  {
    $where = array('id_transaksi' => $id_transaksi);
    // var_dump(1);die;
    $data['data_wirecu_update'] = $this->M_admin->get_data('tb_wirecu_masuk',$where);
    $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->load->view('admin/form_wirecumasuk/form_update_wirecu',$data);
  }

  public function delete_wirecu($id_transaksi)
  {
    $where = array('id_transaksi' => $id_transaksi);
    $this->M_admin->delete('tb_wirecu_masuk',$where);
    redirect(base_url('admin/tabel_wirecumasuk'));
  }


  public function proses_datawirecu_masuk_insert()
  {
    $this->form_validation->set_rules('wirecutype','Wirecutype','required');
    $this->form_validation->set_rules('lotnumber','Lotnumber','required');
    $this->form_validation->set_rules('partno','Partno','required');
    $this->form_validation->set_rules('jumlah','Jumlah','required');

    if($this->form_validation->run() == TRUE)
    {
      $id_transaksi  = $this->input->post('id_transaksi',TRUE);
      $supplier      = $this->input->post('supplier',TRUE);
      $wirecutype    = $this->input->post('wirecutype',TRUE);
      $partno        = $this->input->post('partno',TRUE);
      $lotnumber     = $this->input->post('lotnumber',TRUE);
      $expdate       = $this->input->post('expdate',TRUE);
      $jumlah        = $this->input->post('jumlah',TRUE);
      $remarks       = $this->input->post('remarks',TRUE);

      $data = array(
            'id_transaksi' => $id_transaksi,    
            'supplier'     => $supplier, 
            'wirecutype'   => $wirecutype,
            'partno'       => $partno,
            'lotnumber'    => $lotnumber,
            'expdate'      => $expdate,
            'jumlah'       => $jumlah,
            'remarks'      => $remarks
      );
      $this->M_admin->insert('tb_wirecu_masuk',$data);

      $this->session->set_flashdata('msg_berhasil','Data Barang Berhasil Ditambahkan');
      redirect(base_url('admin/form_wirecumasuk'));
    }else {
      $data['list_satuan'] = $this->M_admin->select('tb_satuan');
      $this->load->view('admin/form_wirecumasuk/form_insert_wirecu',$data);
    }
  }

  public function proses_datawirecu_masuk_update()
  {
    $this->form_validation->set_rules('wirecutype','Wirecutype','required');
    $this->form_validation->set_rules('partno','Partno','required');
    $this->form_validation->set_rules('lotnumber','Lotnumber','required');
    $this->form_validation->set_rules('jumlah','Jumlah','required');

    if($this->form_validation->run() == TRUE)
    {
      $id_transaksi  = $this->input->post('id_transaksi',TRUE);
      $supplier      = $this->input->post('supplier',TRUE);
      $wirecutype    = $this->input->post('wirecutype',TRUE);
      $partno        = $this->input->post('partno',TRUE);
      $lotnumber     = $this->input->post('lotnumber',TRUE);
      $expdate       = $this->input->post('expdate',TRUE);
      $jumlah        = $this->input->post('jumlah',TRUE);
      $remarks       = $this->input->post('remarks',TRUE);
 

      $where = array('id_transaksi' => $id_transaksi);
      $data = array(
            'id_transaksi' => $id_transaksi,
            'supplier'     => $supplier,
            'wirecutype'   => $wirecutype,
            'partno'       => $partno,
            'lotnumber'    => $lotnumber,
            'expdate'      => $expdate,
            'jumlah'       => $jumlah,
            'remarks'      => $remarks, 
      );
      $this->M_admin->update('tb_wirecu_masuk',$data,$where);
      $this->session->set_flashdata('msg_berhasil','Data Barang Berhasil Diupdate');
      redirect(base_url('admin/tabel_wirecumasuk'));  
    }else{
      $this->load->view('admin/form_wirecumasuk/form_update_wirecu');
    }
  }

  public function form_wirealumasuk()
  {
    $data['list_satuan'] = $this->M_admin->select('tb_satuan');
    $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->load->view('admin/form_wirealumasuk/form_insert_wirealu',$data);
  }

  public function tabel_wirealumasuk()
  {
    $data = array(
              'list_data' => $this->M_admin->select('tb_wirealu_masuk'),
              'avatar'    => $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'))
            );
    $this->load->view('admin/tabel/tabel_wirealumasuk',$data);
  }

  public function update_wirealu($id_transaksi)
  {
    $where = array('id_transaksi' => $id_transaksi);
    // var_dump(1);die;
    $data['data_wirealu_update'] = $this->M_admin->get_data('tb_wirealu_masuk',$where);
    $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->load->view('admin/form_wirealumasuk/form_update_wirealu',$data);
  }

  public function delete_wirealu($id_transaksi)
  {
    $where = array('id_transaksi' => $id_transaksi);
    $this->M_admin->delete('tb_wirealu_masuk',$where);
    redirect(base_url('admin/tabel_wirealumasuk'));
  }


  public function proses_datawirealu_masuk_insert()
  {
    $this->form_validation->set_rules('wirealutype','Wirealutype','required');
    $this->form_validation->set_rules('lotnumber','Lotnumber','required');
    $this->form_validation->set_rules('partno','Partno','required');
    $this->form_validation->set_rules('jumlah','Jumlah','required');

    if($this->form_validation->run() == TRUE)
    {
      $id_transaksi  = $this->input->post('id_transaksi',TRUE);
      $supplier      = $this->input->post('supplier',TRUE);
      $wirealutype   = $this->input->post('wirealutype',TRUE);
      $partno        = $this->input->post('partno',TRUE);
      $lotnumber     = $this->input->post('lotnumber',TRUE);
      $expdate       = $this->input->post('expdate',TRUE);
      $jumlah        = $this->input->post('jumlah',TRUE);
      $remarks       = $this->input->post('remarks',TRUE);

      $data = array(
            'id_transaksi' => $id_transaksi,
            'supplier'     => $supplier,     
            'wirealutype'  => $wirealutype,
            'partno'       => $partno,
            'lotnumber'    => $lotnumber,
            'expdate'      => $expdate,
            'jumlah'       => $jumlah,
            'remarks'      => $remarks
      );
      $this->M_admin->insert('tb_wirealu_masuk',$data);

      $this->session->set_flashdata('msg_berhasil','Data Barang Berhasil Ditambahkan');
      redirect(base_url('admin/form_wirealumasuk'));
    }else {
      $data['list_satuan'] = $this->M_admin->select('tb_satuan');
      $this->load->view('admin/form_wirealumasuk/form_insert_wirealu',$data);
    }
  }

  public function proses_datawirealu_masuk_update()
  {
    $this->form_validation->set_rules('wirealutype','Wirealutype','required');
    $this->form_validation->set_rules('partno','Partno','required');
    $this->form_validation->set_rules('lotnumber','Lotnumber','required');
    $this->form_validation->set_rules('jumlah','Jumlah','required');

    if($this->form_validation->run() == TRUE)
    {
      $id_transaksi = $this->input->post('id_transaksi',TRUE);
      $supplier     = $this->input->post('supplier',TRUE);
      $wirealutype  = $this->input->post('wirealutype',TRUE);
      $partno       = $this->input->post('partno',TRUE);
      $lotnumber    = $this->input->post('lotnumber',TRUE);
      $expdate      = $this->input->post('expdate',TRUE);
      $jumlah       = $this->input->post('jumlah',TRUE);
      $remarks      = $this->input->post('remarks',TRUE);
 

      $where = array('id_transaksi' => $id_transaksi);
      $data = array(
            'id_transaksi' => $id_transaksi,
            'supplier'     => $supplier,
            'wirealutype'  => $wirealutype,
            'partno'       => $partno,
            'lotnumber'    => $lotnumber,
            'expdate'      => $expdate,
            'jumlah'       => $jumlah,
            'remarks'      => $remarks, 
      );
      $this->M_admin->update('tb_wirealu_masuk',$data,$where);
      $this->session->set_flashdata('msg_berhasil','Data Barang Berhasil Diupdate');
      redirect(base_url('admin/tabel_wirealumasuk'));  
    }else{
      $this->load->view('admin/form_wirecumasuk/form_update_wirealu');
    }
  }
  ####################################
      // END DATA BARANG MASUK
  ####################################


  ####################################
              // SATUAN
  ####################################

  public function form_satuan()
  {
    $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->load->view('admin/form_satuan/form_insert',$data);
  }

  public function tabel_satuan()
  {
    $data['list_data'] = $this->M_admin->select('tb_satuan');
    $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->load->view('admin/tabel/tabel_satuan',$data);
  }

  public function update_satuan()
  {
    $uri = $this->uri->segment(3);
    $where = array('id_satuan' => $uri);
    $data['data_satuan'] = $this->M_admin->get_data('tb_satuan',$where);
    $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->load->view('admin/form_satuan/form_update',$data);
  }

  public function delete_satuan()
  {
    $uri = $this->uri->segment(3);
    $where = array('id_satuan' => $uri);
    $this->M_admin->delete('tb_satuan',$where);
    redirect(base_url('admin/tabel_satuan'));
  }

  public function proses_satuan_insert()
  {
    $this->form_validation->set_rules('kode_satuan','Kode Satuan','trim|required|max_length[100]');
    $this->form_validation->set_rules('nama_satuan','Nama Satuan','trim|required|max_length[100]');

    if($this->form_validation->run() ==  TRUE)
    {
      $kode_satuan = $this->input->post('kode_satuan' ,TRUE);
      $nama_satuan = $this->input->post('nama_satuan' ,TRUE);

      $data = array(
            'kode_satuan' => $kode_satuan,
            'nama_satuan' => $nama_satuan
      );
      $this->M_admin->insert('tb_satuan',$data);

      $this->session->set_flashdata('msg_berhasil','Data satuan Berhasil Ditambahkan');
      redirect(base_url('admin/form_satuan'));
    }else {
      $this->load->view('admin/form_satuan/form_insert');
    }
  }

  public function proses_satuan_update()
  {
    $this->form_validation->set_rules('kode_satuan','Kode Satuan','trim|required|max_length[100]');
    $this->form_validation->set_rules('nama_satuan','Nama Satuan','trim|required|max_length[100]');

    if($this->form_validation->run() ==  TRUE)
    {
      $id_satuan   = $this->input->post('id_satuan' ,TRUE);
      $kode_satuan = $this->input->post('kode_satuan' ,TRUE);
      $nama_satuan = $this->input->post('nama_satuan' ,TRUE);

      $where = array(
            'id_satuan' => $id_satuan
      );

      $data = array(
            'kode_satuan' => $kode_satuan,
            'nama_satuan' => $nama_satuan
      );
      $this->M_admin->update('tb_satuan',$data,$where);

      $this->session->set_flashdata('msg_berhasil','Data satuan Berhasil Di Update');
      redirect(base_url('admin/tabel_satuan'));
    }else {
      $this->load->view('admin/form_satuan/form_update');
    }
  }

  ####################################
            // END SATUAN
  ####################################


  ####################################
     // DATA MASUK KE DATA KELUAR
  ####################################

  public function barang_keluar()
  {
    $uri = $this->uri->segment(3);
    $where = array( 'id_transaksi' => $uri);
    $data['list_data'] = $this->M_admin->get_data('tb_barang_masuk',$where);
    $data['list_satuan'] = $this->M_admin->select('tb_satuan');
    $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->load->view('admin/perpindahan_barang/form_update',$data);
  }

  public function proses_data_keluar()
  {
    $this->form_validation->set_rules('tanggal_keluar','Tanggal Keluar','trim|required');
    if($this->form_validation->run() === TRUE)
    {
      $id_transaksi   = $this->input->post('id_transaksi',TRUE);
      $tanggal_masuk  = $this->input->post('tanggal',TRUE);
      $tanggal_keluar = $this->input->post('tanggal_keluar',TRUE);
      $lokasi         = $this->input->post('lokasi',TRUE);
      $kode_barang    = $this->input->post('kode_barang',TRUE);
      $nama_barang    = $this->input->post('nama_barang',TRUE);
      $satuan         = $this->input->post('satuan',TRUE);
      $jumlah         = $this->input->post('jumlah',TRUE);

      $where = array( 'id_transaksi' => $id_transaksi);
      $data = array(
              'id_transaksi' => $id_transaksi,
              'tanggal_masuk' => $tanggal_masuk,
              'tanggal_keluar' => $tanggal_keluar,
              'lokasi' => $lokasi,
              'kode_barang' => $kode_barang,
              'nama_barang' => $nama_barang,
              'satuan' => $satuan,
              'jumlah' => $jumlah
      );
        $this->M_admin->insert('tb_barang_keluar',$data);
        $this->session->set_flashdata('msg_berhasil_keluar','Data Berhasil Keluar');
        redirect(base_url('admin/tabel_barangmasuk'));
    }else {
      $this->load->view('perpindahan_barang/form_update/'.$id_transaksi);
    }

  }

  public function compound_keluar()
  {
    $uri = $this->uri->segment(3);
    $where = array( 'id_transaksi' => $uri);
    $data['list_data'] = $this->M_admin->get_data('tb_compound_masuk',$where);
    // var_dump($data);die;
    $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->load->view('admin/perpindahan_barang/form_update_compound',$data);
  }

  public function proses_compound_keluar()
  {
    $this->form_validation->set_rules('expdate','expdate','trim|required');
    if($this->form_validation->run() === TRUE)
    {
      $id_transaksi = $this->input->post('id_transaksi',TRUE);
      $supplier     = $this->input->post('supplier',TRUE);
      $partno       = $this->input->post('partno',TRUE);
      $lotnumber    = $this->input->post('lotnumber',TRUE);
      $expdate      = $this->input->post('expdate',TRUE);
      $jumlah       = $this->input->post('jumlah',TRUE);
      $remarks      = $this->input->post('remarks',TRUE);

      $where = array( 'id_transaksi' => $id_transaksi);
      $data = array(
              'id_transaksi' => $id_transaksi,
              'supplier'     => $supplier,
              'partno'       => $partno,
              'lotnumber'    => $lotnumber,
              'expdate'      => $expdate,
              'jumlah'       => $jumlah,
              'remarks'      => $remarks
      );
      // var_dump($data);die;
        $this->M_admin->insert('tb_compound_keluar',$data);
        $this->session->set_flashdata('msg_berhasil_keluar','Data Berhasil Keluar');
        redirect(base_url('admin/tabel_compoundmasuk'));
    }else {
      $this->load->view('perpindahan_barang/form_update_compound'.$id_transaksi);
    }

  }

  public function leadframe_keluar()
  {
    $uri = $this->uri->segment(3);
    $where = array( 'id_transaksi' => $uri);
    $data['list_data'] = $this->M_admin->get_data('tb_leadframe_masuk',$where);
    // var_dump($data);die;
    $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->load->view('admin/perpindahan_barang/form_update_leadframe',$data);
  }

  public function proses_leadframe_keluar()
  {
    $this->form_validation->set_rules('expdate','expdate','trim|required');
    if($this->form_validation->run() === TRUE)
    {
      $id_transaksi = $this->input->post('id_transaksi',TRUE);
      $supplier     = $this->input->post('supplier',TRUE);
      $partno       = $this->input->post('partno',TRUE);
      $lotnumber    = $this->input->post('lotnumber',TRUE);
      $expdate      = $this->input->post('expdate',TRUE);
      $jumlah       = $this->input->post('jumlah',TRUE);
      $remarks      = $this->input->post('remarks',TRUE);

      $where = array( 'id_transaksi' => $id_transaksi);
      $data = array(
              'id_transaksi'  => $id_transaksi,
              'supplier'      => $supplier,
              'partno'        => $partno,
              'lotnumber'     => $lotnumber,
              'expdate'       => $expdate,
              'jumlah'        => $jumlah,
              'remarks'       => $remarks
      );
      // var_dump($data);die;
        $this->M_admin->insert('tb_leadframe_keluar',$data);
        $this->session->set_flashdata('msg_berhasil_keluar','Data Berhasil Keluar');
        redirect(base_url('admin/tabel_leadframemasuk'));
    }else {
      $this->load->view('perpindahan_barang/form_update_leadframe'.$id_transaksi);
    }

  }

  public function glue_keluar()
  {
    $uri = $this->uri->segment(3);
    $where = array( 'id_transaksi' => $uri);
    $data['list_data'] = $this->M_admin->get_data('tb_glue_masuk',$where);
    // var_dump($data);die;
    $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->load->view('admin/perpindahan_barang/form_update_glue',$data);
  }

  public function proses_glue_keluar()
  {
    $this->form_validation->set_rules('expdate','expdate','trim|required');
    if($this->form_validation->run() === TRUE)
    {
      $id_transaksi  = $this->input->post('id_transaksi',TRUE);
      $supplier      = $this->input->post('supplier',TRUE);
      $gluetype      = $this->input->post('gluetype',TRUE);
      $partno        = $this->input->post('partno',TRUE);
      $lotnumber     = $this->input->post('lotnumber',TRUE);
      $expdate       = $this->input->post('expdate',TRUE);
      $jumlah        = $this->input->post('jumlah',TRUE);
      $remarks       = $this->input->post('remarks',TRUE);

      $where = array( 'id_transaksi' => $id_transaksi);
      $data = array(
         
              'id_transaksi' => $id_transaksi,     
              'supplier'     => $supplier,
              'gluetype'     => $gluetype,
              'partno'       => $partno,
              'lotnumber'    => $lotnumber,
              'expdate'      => $expdate,
              'jumlah'       => $jumlah,
              'remarks'      => $remarks
      );
      // var_dump($data);die;
        $this->M_admin->insert('tb_glue_keluar',$data);
        $this->session->set_flashdata('msg_berhasil_keluar','Data Berhasil Keluar');
        redirect(base_url('admin/tabel_gluemasuk'));
    }else {
      $this->load->view('perpindahan_barang/form_update_glue'.$id_transaksi);
    }

  }

  public function wafer_keluar()
  {
    $uri = $this->uri->segment(3);
    $where = array( 'id_transaksi' => $uri);
    $data['list_data'] = $this->M_admin->get_data('tb_wafer_masuk',$where);
    // var_dump($data);die;
    $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->load->view('admin/perpindahan_barang/form_update_wafer',$data);
  }

  public function proses_wafer_keluar()
  {
    $this->form_validation->set_rules('expdate','expdate','trim|required');
    if($this->form_validation->run() === TRUE)
    {
      $id_transaksi = $this->input->post('id_transaksi',TRUE);
      $supplier     = $this->input->post('supplier',TRUE);
      $wafertype    = $this->input->post('wafertype',TRUE);
      $pellet       = $this->input->post('pellet',TRUE);
      $partno       = $this->input->post('partno',TRUE);
      $lotnumber    = $this->input->post('lotnumber',TRUE);
      $expdate      = $this->input->post('expdate',TRUE);
      $jumlah       = $this->input->post('jumlah',TRUE);
      $remarks      = $this->input->post('remarks',TRUE);

      $where = array( 'id_transaksi' => $id_transaksi);
      $data = array(
        
              'id_transaksi' => $id_transaksi,
              'supplier'     => $supplier,
              'wafertype'    => $wafertype,
              'pellet'       => $pellet,
              'partno'       => $partno,
              'lotnumber'    => $lotnumber,
              'expdate'      => $expdate,
              'jumlah'       => $jumlah,
              'remarks'      => $remarks
      );
      // var_dump($data);die;
        $this->M_admin->insert('tb_wafer_keluar',$data);
        $this->session->set_flashdata('msg_berhasil_keluar','Data Berhasil Keluar');
        redirect(base_url('admin/tabel_wafermasuk'));
    }else {
      $this->load->view('perpindahan_barang/form_update_wafer'.$id_transaksi);
    }

  }

  public function solder_keluar()
  {
    $uri = $this->uri->segment(3);
    $where = array( 'id_transaksi' => $uri);
    $data['list_data'] = $this->M_admin->get_data('tb_solder_masuk',$where);
    // var_dump($data);die;
    $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->load->view('admin/perpindahan_barang/form_update_solderwire',$data);
  }

  public function proses_solder_keluar()
  {
    $this->form_validation->set_rules('expdate','expdate','trim|required');
    if($this->form_validation->run() === TRUE)
    {
      $id_transaksi = $this->input->post('id_transaksi',TRUE);
      $supplier     = $this->input->post('supplier',TRUE);
      $soldertype   = $this->input->post('soldertype',TRUE);
      $partno       = $this->input->post('partno',TRUE);
      $lotnumber    = $this->input->post('lotnumber',TRUE);
      $expdate      = $this->input->post('expdate',TRUE);
      $jumlah       = $this->input->post('jumlah',TRUE);
      $remarks      = $this->input->post('remarks',TRUE);

      $where = array( 'id_transaksi' => $id_transaksi);
      $data = array(
        
              'id_transaksi'  => $id_transaksi,
              'supplier'      => $supplier,
              'soldertype'    => $soldertype,
              'partno'        => $partno,
              'lotnumber'     => $lotnumber,
              'expdate'       => $expdate,
              'jumlah'        => $jumlah,
              'remarks'       => $remarks
      );
      // var_dump($data);die;
        $this->M_admin->insert('tb_solder_keluar',$data);
        $this->session->set_flashdata('msg_berhasil_keluar','Data Berhasil Keluar');
        redirect(base_url('admin/tabel_solderwiremasuk'));
    }else {
      $this->load->view('perpindahan_barang/form_update_solderwire'.$id_transaksi);
    }

  }

  public function wireau_keluar()
  {
    $uri = $this->uri->segment(3);
    $where = array( 'id_transaksi' => $uri);
    $data['list_data'] = $this->M_admin->get_data('tb_wireau_masuk',$where);
    // var_dump($data);die;
    $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->load->view('admin/perpindahan_barang/form_update_wireau',$data);
  }

  public function proses_wireau_keluar()
  {
    $this->form_validation->set_rules('expdate','expdate','trim|required');
    if($this->form_validation->run() === TRUE)
    {
      $id_transaksi  = $this->input->post('id_transaksi',TRUE);
      $supplier      = $this->input->post('supplier',TRUE);
      $wireautype    = $this->input->post('wireautype',TRUE);
      $partno        = $this->input->post('partno',TRUE);
      $lotnumber     = $this->input->post('lotnumber',TRUE);
      $expdate       = $this->input->post('expdate',TRUE);
      $jumlah        = $this->input->post('jumlah',TRUE);
      $remarks       = $this->input->post('remarks',TRUE);

      $where = array( 'id_transaksi' => $id_transaksi);
      $data = array(
        
              'id_transaksi' => $id_transaksi,
              'supplier'     => $supplier,
              'wireautype'   => $wireautype,
              'partno'       => $partno,
              'lotnumber'    => $lotnumber,
              'expdate'      => $expdate,
              'jumlah'       => $jumlah,
              'remarks'      => $remarks
      );
      // var_dump($data);die;
        $this->M_admin->insert('tb_wireau_keluar',$data);
        $this->session->set_flashdata('msg_berhasil_keluar','Data Berhasil Keluar');
        redirect(base_url('admin/tabel_wireaum'));
    }else {
      $this->load->view('perpindahan_barang/form_update_wireau'.$id_transaksi);
    }

  }

  public function wirecu_keluar()
  {
    $uri = $this->uri->segment(3);
    $where = array( 'id_transaksi' => $uri);
    $data['list_data'] = $this->M_admin->get_data('tb_wirecu_masuk',$where);
    // var_dump($data);die;
    $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->load->view('admin/perpindahan_barang/form_update_wirecu',$data);
  }

  public function proses_wirecu_keluar()
  {
    $this->form_validation->set_rules('expdate','expdate','trim|required');
    if($this->form_validation->run() === TRUE)
    {
      $id_transaksi = $this->input->post('id_transaksi',TRUE);
      $supplier     = $this->input->post('supplier',TRUE);
      $wirecutype   = $this->input->post('wirecutype',TRUE);
      $partno       = $this->input->post('partno',TRUE);
      $lotnumber    = $this->input->post('lotnumber',TRUE);
      $expdate      = $this->input->post('expdate',TRUE);
      $jumlah       = $this->input->post('jumlah',TRUE);
      $remarks      = $this->input->post('remarks',TRUE);

      $where = array( 'id_transaksi' => $id_transaksi);
      $data = array(
        
              'id_transaksi' => $id_transaksi,
              'supplier'     => $supplier,
              'wirecutype'   => $wirecutype,
              'partno'       => $partno,
              'lotnumber'    => $lotnumber,
              'expdate'      => $expdate,
              'jumlah'       => $jumlah,
              'remarks'      => $remarks
      );
      // var_dump($data);die;
        $this->M_admin->insert('tb_wirecu_keluar',$data);
        $this->session->set_flashdata('msg_berhasil_keluar','Data Berhasil Keluar');
        redirect(base_url('admin/tabel_wirecumasuk'));
    }else {
      $this->load->view('perpindahan_barang/form_update_wirecu'.$id_transaksi);
    }

  }

  public function wirealu_keluar()
  {
    $uri = $this->uri->segment(3);
    $where = array( 'id_transaksi' => $uri);
    $data['list_data'] = $this->M_admin->get_data('tb_wirealu_masuk',$where);
    // var_dump($data);die;
    $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->load->view('admin/perpindahan_barang/form_update_wirealu',$data);
  }

  public function proses_wirealu_keluar()
  {
    $this->form_validation->set_rules('expdate','expdate','trim|required');
    if($this->form_validation->run() === TRUE)
    {
      $id_transaksi  = $this->input->post('id_transaksi',TRUE);
      $supplier      = $this->input->post('supplier',TRUE); 
      $wirealutype   = $this->input->post('wirealutype',TRUE);
      $partno        = $this->input->post('partno',TRUE);
      $lotnumber     = $this->input->post('lotnumber',TRUE);
      $expdate       = $this->input->post('expdate',TRUE);
      $jumlah        = $this->input->post('jumlah',TRUE);
      $remarks       = $this->input->post('remarks',TRUE);

      $where = array( 'id_transaksi' => $id_transaksi);
      $data = array(
        
              'id_transaksi' => $id_transaksi,
              'supplier'     => $supplier,
              'wirealutype'  => $wirealutype,
              'partno'       => $partno,
              'lotnumber'    => $lotnumber,
              'expdate'      => $expdate,
              'jumlah'       => $jumlah,
              'remarks'      => $remarks
      );
      // var_dump($data);die;
        $this->M_admin->insert('tb_wirealu_keluar',$data);
        $this->session->set_flashdata('msg_berhasil_keluar','Data Berhasil Keluar');
        redirect(base_url('admin/tabel_wirealumasuk'));
    }else {
      $this->load->view('perpindahan_barang/form_update_wirealu'.$id_transaksi);
    }

  }
  ####################################
    // END DATA MASUK KE DATA KELUAR
  ####################################


  ####################################
        // DATA BARANG KELUAR
  ####################################

  public function tabel_barangkeluar()
  {
    $data['list_data'] = $this->M_admin->select('tb_barang_keluar');
    $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->load->view('admin/tabel/tabel_barangkeluar',$data);
  }

  public function tabel_compoundkeluar()
  {
    $data['list_data'] = $this->M_admin->select('tb_compound_keluar');
    $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->load->view('admin/tabel/tabel_compoundkeluar',$data);
  }

  public function tabel_expiredcompound()
  {
    $data['list_data'] = $this->M_admin->select('tb_compound_masuk');
    $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->load->view('admin/tabel/tabel_expiredcompound',$data);
  }

  public function tabel_leadframekeluar()
  {
    $data['list_data'] = $this->M_admin->select('tb_leadframe_keluar');
    $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->load->view('admin/tabel/tabel_leadframekeluar',$data);
  }

  public function tabel_gluekeluar()
  {
    $data['list_data'] = $this->M_admin->select('tb_glue_keluar');
    $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->load->view('admin/tabel/tabel_gluekeluar',$data);
  }

  public function tabel_waferkeluar()
  {
    $data['list_data'] = $this->M_admin->select('tb_wafer_keluar');
    $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->load->view('admin/tabel/tabel_waferkeluar',$data);
  }

  public function tabel_solderwirekeluar()
  {
    $data['list_data'] = $this->M_admin->select('tb_solder_keluar');
    $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->load->view('admin/tabel/tabel_solderwirekeluar',$data);
  }

  public function tabel_wireauk()
  {
    $data['list_data'] = $this->M_admin->select('tb_wireau_keluar');
    $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->load->view('admin/tabel/tabel_wireauk',$data);
  }

  public function tabel_wirecukeluar()
  {
    $data['list_data'] = $this->M_admin->select('tb_wirecu_keluar');
    $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->load->view('admin/tabel/tabel_wirecukeluar',$data);
  }

  public function tabel_wirealukeluar()
  {
    $data['list_data'] = $this->M_admin->select('tb_wirealu_keluar');
    $data['avatar'] = $this->M_admin->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->load->view('admin/tabel/tabel_wirealukeluar',$data);
  }

 public function excel(){
  
  $data['tabel_compoundmasuk'] = $this->tabel_compoundmasuk->tampil_data('tb_compound_masuk')->result();

  require(APPPATH. 'PHPExcel-1.8/classes/PHPExcel.php');
  require(APPPATH. 'PHPExcel-1.8/classes/PHPExcel/Writer/Excel2007.php');

  $object = new PHPExceL();

  $object->getProperties()->setCreator("Framework Indonesia");
  $object->getProperties()->setLastModifiedBy("Framework Indonesia");
  $object->getProperties()->setTitle("Daftar Compound");

  $object->setActiveSheetIndex(0);

  $object->getActiveSheet()->setCellValue('A1', 'NO');
  $object->getActiveSheet()->setCellValue('B1', 'ID Transaksi');
  $object->getActiveSheet()->setCellValue('C1', 'Supplier');
  $object->getActiveSheet()->setCellValue('D1', 'Part No');
  $object->getActiveSheet()->setCellValue('E1', 'Lot Number');
  $object->getActiveSheet()->setCellValue('F1', 'Exp Date');
  $object->getActiveSheet()->setCellValue('G1', 'Quantity');
  $object->getActiveSheet()->setCellValue('H1', 'Remarks');

  $baris = 2;
  $no = 1;

  foreach ($data['tabel_compoundmasuk']  as $tabel_compoundmasuk){
    $object->getActiveSheet()->setCellValue('A' .$baris, $no++);
    $object->getActiveSheet()->setCellValue('B' .$baris, $tabel_compoundmasuk->id_transaksi);
    $object->getActiveSheet()->setCellValue('C' .$Baris, $tabel_compoundmasuk->supplier);
    $object->getActiveSheet()->setCellValue('D' .$Baris, $tabel_compoundmasuk->partno);
    $object->getActiveSheet()->setCellValue('E' .$Baris, $tabel_compoundmasuk->lotnumber);
    $object->getActiveSheet()->setCellValue('F' .$Baris, $tabel_compoundmasuk->expdate);
    $object->getActiveSheet()->setCellValue('G' .$Baris, $tabel_compoundmasuk->quantity);
    $object->getActiveSheet()->setCellValue('H' .$Baris, $tabel_compoundmasuk->remarks);

    $baris++;

  }

  $filename="tb_compound_masuk".'.xlsx';

  $object->getActiveSheet()->setTitle("tabel_compundmasuk");

  header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
  header('Content-Disposition: attachment;filename"'.$filename. '"');
  header('Cache-Control: max-age=0');

  $Writer=PHPExceL_IOFactory::createwriter($object, 'Excel2007');
  $Writer->save('php://output');

  exit;
 }
} 
?>
