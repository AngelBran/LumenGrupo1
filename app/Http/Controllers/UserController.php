<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
// Email
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::where('register_status',"=",1)->get([
            'names',
            'lastnames',
            'username',
            'email'
        ]);

        return $users;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $users = new User();

        function code($limit) { 
            $chars = "abcdefghijkmnopqrstuvwxyz023456789"; 
            srand((double)microtime()*1000000); 
            $i = 0; 
            $codigo = '' ; 

            for ($i=0; $i <= $limit; $i++) { 
                $num = rand() % 33; 
                $tmp = substr($chars, $num, 1); 
                $codigo = $codigo . $tmp; 
            }

            return $codigo;
        }
      
        $users->names = $request->names;
        $users->lastnames = $request->lastnames;
        $users->username = $request->username;
        $users->email = $request->email;
        $users->birthday= $request->birthday;
        $users->phone = $request->phone;
        $users->password = code(10);
        $users->code = code(7);
        $users->register_status = 0;
        $users->save();

        $this->email($users->email, $users->password, $users->names . " " . $users->lastnames, $users->code);

        return $users;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    function email($email, $password, $names, $code){
        $mail = new PHPMailer(true);

        try {
            //Server settings
            #$mail->SMTPDebug = SMTP::DEBUG_SERVER;
            //Enable verbose debug output
            $mail->isSMTP();                                      //Send using SMTP
            $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = 'bootcampproyecto@gmail.com';                     //SMTP username
            $mail->Password   = 'lwsksotsietdlkxk';                               //SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
            $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

            //Recipients
            $mail->setFrom('bootcampproyecto@gmail.com');
            $mail->addAddress($email, $names);
            
            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = 'Confirmacion de correo';
            $mail->Body    = 'Hola ' . $names . '<br>Su contraseña es: <strong>' . $password . "</strong><br><a href=\"http://localhost:8000/usuarios/confirm/$code\">Verificar correo electrónico</a>";

            $mail->send();
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }
}
