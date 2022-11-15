<?php
defined('BASEPATH') or exit('No direct script access allowed');


class Notemaker extends CI_Controller
{

    //----------------------------------->User
    public function userSignup()
    {
        $responseData = [];
        $data = [];
        $this->form_validation->set_rules("name", "UserName", "required");
        $this->form_validation->set_rules("password", "Userpassword", "required");

        if ($this->form_validation->run() === FALSE) {
            $responseData["Status"] = "validation error";
            header("Content-Type: application/json; charset=utf-8");
            echo json_encode($responseData);
        } else {
            $userDetails = $this->UserModel->createUser();
            $this->UserModel->uforgeTabledata($userDetails);
            $data["User"] = $userDetails;
            $responseData["Status"] =  'Successfully create un_' . $userDetails . ' tablename';
            header("Content-Type: application/json; charset=utf-8");
            echo json_encode($responseData);
        }
    }

    public function login()
    {
        $responseData = [];
        $data = [];
        $isloginUser = $this->UserModel->logInUser($this->input->post('name'), $this->input->post('password'));
        if (!empty($isloginUser[0]->id)) {
            $this->session->set_userdata("userid", $isloginUser[0]->id);
            $data['User'] = $isloginUser[0]->id;
            $responseData["Status"] = "validation Successfully userid : " . $data['User'];
            header("Content-Type: application/json; charset=utf-8");
            echo json_encode($responseData);
        } else {
            $responseData["Status"] = "validation error";
            header("Content-Type: application/json; charset=utf-8");
            echo json_encode($responseData);
        }
    }

    public function makingNotes()
    {
        $responseData = [];
        // $data = [];
        $islogin = $this->UserModel->isLogin();
        if ($islogin) {
            $this->UserModel->createUserNotes($this->session->userdata('userid'));
            $responseData["Status"] = "created Successfully";
            header("Content-Type: application/json; charset=utf-8");
            echo json_encode($responseData);
        } else {
            $responseData["Status"] = " unable create";
            header("Content-Type: application/json; charset=utf-8");
            echo json_encode($responseData);
        }
    }

    public function editUser($id)
    {
        $responseData = [];
        if ($this->UserModel->isLogin()) {
            if ($this->UserModel->usercheck($id)) {
                $this->UserModel->updateUser($id);
                $responseData["Status"] = "updated Successfully";
                header("Content-Type: application/json; charset=utf-8");
                echo json_encode($responseData);
            } else {
                $responseData["Status"] = "updated unSuccessful";
                header("Content-Type: application/json; charset=utf-8");
                echo json_encode($responseData);
            }
        } else {
            $responseData["Status"] = "login unSuccessful";
            header("Content-Type: application/json; charset=utf-8");
            echo json_encode($responseData);
        }
    }


    public function editNotes($id)
    {
        $responseData = [];
        if ($this->UserModel->isLogin()) {
            $sessionid = $this->session->userdata('userid');
            $this->UserModel->updateNote($id, $sessionid);
            $responseData["Status"] = "updated Successfully";
            header("Content-Type: application/json; charset=utf-8");
            echo json_encode($responseData);
        } else {
            $responseData["Status"] = "updated unSuccessful";
            header("Content-Type: application/json; charset=utf-8");
            echo json_encode($responseData);
        }
    }


    public function deleteNote($id)
    {
        $responseData = [];
        if ($this->UserModel->isLogin()) {
            $this->UserModel->deleteupdateNote($id);
            $responseData["Status"] = " deleted Successfully";
            header("Content-Type: application/json; charset=utf-8");
            echo json_encode($responseData);
        } else {
            $responseData["Status"] = "deleted unSuccessful";
            header("Content-Type: application/json; charset=utf-8");
            echo json_encode($responseData);
        }
    }

    public function deleteUser($id)
    {
        $responseData = [];
        if ($this->UserModel->isLogin()) {
            if ($this->UserModel->usercheck($id)) {
                $this->UserModel->deleteupdateusers($id);
                $responseData["Status"] = " deleted Successfully";
                header("Content-Type: application/json; charset=utf-8");
                echo json_encode($responseData);
            } else {
                $responseData["Status"] = "deleted unSuccessful";
                header("Content-Type: application/json; charset=utf-8");
                echo json_encode($responseData);
            }
        } else {
            $responseData["Status"] = "login unSuccessful";
            header("Content-Type: application/json; charset=utf-8");
            echo json_encode($responseData);
        }
    }

    public function viewAllNotes()
    {
        $responseData = new stdClass();
        $user = [];
        if ($this->UserModel->isLogin()) {
            $userView = $this->UserModel->usercheck();
            foreach ($userView as $userViews) {
                $userTemp = [];
                $userTemp["id"] = $userViews->id;
                $userTemp["name"] = $userViews->name;
                $userNotes = [];
                $userNotesviewall = $this->UserModel->extractAllNotes();
                foreach ($userNotesviewall as $userNotesviewalls) {
                    $userNotetemp = [];
                    $userNotetemp['id'] = $userNotesviewalls->id;
                    $userNotetemp['title'] = $userNotesviewalls->title;
                    $userNotetemp['notes'] = $userNotesviewalls->title;
                    array_push($userNotes, $userNotetemp);
                }
                $userTemp["notes"] = $userNotes;
                array_push($user, $userTemp);
            }
            $responseData->status = "success";
            $responseData->user = $user;
            header("Content-Type: application/json; charset=utf-8");
            $jsonResponse =  json_encode($responseData);
            echo ($jsonResponse);
        } else {
            $responseData->status = "unsuccessfully user login falied";
            header("Content-Type: application/json; charset=utf-8");
            $jsonResponse =  json_encode($responseData);
            echo ($jsonResponse);
        }
    }


    public function destroyOut()
    {
        $responseData = [];
        $this->session->sess_destroy();
        $responseData["Status"] = "Successfully Log out";
        header("Content-Type: application/json; charset=utf-8");
        echo json_encode($responseData);
    }
}
