<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller
{

  function __construct()
  {
    parent::__construct();
    $this->load->model('M_user');
  }

  public function index()
  {
    if($this->session->userdata('status') == 'login' && $this->session->userdata('role') == 0)
    {
     
      $this->load->view('user/index');
    }else {
      $this->load->view('login/login',$data);
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
    $data['avatar'] = $this->M_user->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->session->set_userdata($data);
    $this->load->view('user/profile',$data);
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
        redirect(base_url('user/profile'));
      }
    }else {
      $this->load->view('user/profile');
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
        $this->load->view('user/profile',$data);
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
          $this->load->view('user/profile',$data);
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
        redirect(base_url('user/profile'));
      }
  }

   ####################################
           // End Profile
  ####################################

  ####################################
        // DATA BARANG MASUK
  ####################################

  public function tabel_barangmasuk()
  {
    $this->load->view('user/templates/header.php');
    $data['list_data'] = $this->M_user->select('tb_barang_masuk');
    $this->load->view('user/tabel/tabel_barangmasuk',$data);
    $this->load->view('user/templates/footer.php');
  }
  public function tabel_compoundmasuk()
  {
    $this->load->view('user/templates/header.php');
    $data['list_data'] = $this->M_user->select('tb_compound_masuk');
    $this->load->view('user/tabel/tabel_compoundmasuk',$data);
    $this->load->view('user/templates/footer.php');
  }

  public function tabel_leadframemasuk()
  {   
    $data = array(
              'list_data' => $this->M_user->select('tb_leadframe_masuk'),
              'avatar'    => $this->M_user->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'))
            );
    $this->load->view('user/tabel/tabel_leadframemasuk',$data);
  }

  public function tabel_gluemasuk()
  {
    $data = array(
              'list_data' => $this->M_user->select('tb_glue_masuk'),
              
            );
    $this->load->view('user/tabel/tabel_gluemasuk',$data);
  }

  public function tabel_wafermasuk()
  {
    $this->load->view('user/templates/header.php');
    $data['list_data'] = $this->M_user->select('tb_wafer_masuk');
    $this->load->view('user/tabel/tabel_wafermasuk',$data);
    $this->load->view('user/templates/footer.php');
  }

  public function tabel_solderwiremasuk()
  {
    $this->load->view('user/templates/header.php');
    $data['list_data'] = $this->M_user->select('tb_solder_masuk');
    $this->load->view('user/tabel/tabel_solderwiremasuk',$data);
    $this->load->view('user/templates/footer.php');
  }

  public function tabel_wireaum()
  {
    $this->load->view('user/templates/header.php');
    $data['list_data'] = $this->M_user->select('tb_wireau_masuk');
    $this->load->view('user/tabel/tabel_wireaum',$data);
    $this->load->view('user/templates/footer.php');
  }

  public function tabel_wirecumasuk()
  {
    $this->load->view('user/templates/header.php');
    $data['list_data'] = $this->M_user->select('tb_wirecu_masuk');
    $this->load->view('user/tabel/tabel_wirecumasuk',$data);
    $this->load->view('user/templates/footer.php');
  }

  public function tabel_wirealumasuk()
  {
    $this->load->view('user/templates/header.php');
    $data['list_data'] = $this->M_user->select('tb_wirealu_masuk');
    $this->load->view('user/tabel/tabel_wirealumasuk',$data);
    $this->load->view('user/templates/footer.php');
  }


  ####################################
        // DATA BARANG KELUAR
  ####################################

  public function tabel_barangkeluar()
  {
    $this->load->view('user/templates/header.php');
    $data['list_data'] = $this->M_user->select('tb_barang_keluar');
    $this->load->view('user/tabel/tabel_barangkeluar',$data);
    $this->load->view('user/templates/footer.php');
  }

  public function tabel_compoundkeluar()
  {
    $this->load->view('user/templates/header.php');
    $data['list_data'] = $this->M_user->select('tb_compound_keluar');
    $this->load->view('user/tabel/tabel_compoundkeluar',$data);
    $this->load->view('user/templates/footer.php');
  }

  public function tabel_leadframekeluar()
  {
    $data['list_data'] = $this->M_user->select('tb_leadframe_keluar');
    $data['avatar'] = $this->M_user->get_data_gambar('tb_upload_gambar_user',$this->session->userdata('name'));
    $this->load->view('user/tabel/tabel_leadframekeluar',$data);
  }

  public function tabel_gluekeluar()
  {
    $this->load->view('user/templates/header.php');
    $data['list_data'] = $this->M_user->select('tb_glue_keluar');
    $this->load->view('user/tabel/tabel_gluekeluar',$data);
    $this->load->view('user/templates/footer.php');
  }

  public function tabel_waferkeluar()
  {
    $this->load->view('user/templates/header.php');
    $data['list_data'] = $this->M_user->select('tb_wafer_keluar');
    $this->load->view('user/tabel/tabel_waferkeluar',$data);
    $this->load->view('user/templates/footer.php');
  }

  public function tabel_solderwirekeluar()
  {
    $this->load->view('user/templates/header.php');
    $data['list_data'] = $this->M_user->select('tb_solder_keluar');
    $this->load->view('user/tabel/tabel_solderwirekeluar',$data);
    $this->load->view('user/templates/footer.php');
  }

  public function tabel_wireauk()
  {
    $this->load->view('user/templates/header.php');
    $data['list_data'] = $this->M_user->select('tb_wireau_keluar');
    $this->load->view('user/tabel/tabel_wireauk',$data);
    $this->load->view('user/templates/footer.php');
  }

  public function tabel_wirecukeluar()
  {
    $this->load->view('user/templates/header.php');
    $data['list_data'] = $this->M_user->select('tb_wirecu_keluar');
    $this->load->view('user/tabel/tabel_wirecukeluar',$data);
    $this->load->view('user/templates/footer.php');
  }

  public function tabel_wirealukeluar()
  {
    $this->load->view('user/templates/header.php');
    $data['list_data'] = $this->M_user->select('tb_wirealu_keluar');
    $this->load->view('user/tabel/tabel_wirealukeluar',$data);
    $this->load->view('user/templates/footer.php');
  }

}

?>
