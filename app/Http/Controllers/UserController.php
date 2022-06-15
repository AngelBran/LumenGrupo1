<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

use App\Http\Requests\RegistroRequest;
use App\Http\Requests\LoginRequest;
use Carbon\Carbon;
// Email
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use Illuminate\Support\Facades\Hash;

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

    public function confirm($code) {
        $confirm = User::where('code', $code)->get()->first();

        if (empty($confirm)) {
            return "No existe el código de confirmación";
        }
        else {
            $confirm->code = "";
            $confirm->register_status = 1;
            $confirm->save();

            return "Correo electrónico verificado";
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RegistroRequest $request)
    {
        $request->validate();
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
        
        $password = code(10);

        $users->names = $request->names;
        $users->lastnames = $request->lastnames;
        $users->username = $request->username;
        $users->email = $request->email;
        $users->birthday= $request->birthday;
        $users->phone = $request->phone;
        $users->password = Hash::make($password);
        $users->code = code(7);
        $users->register_status = 0;
        $users->save();

        $this->email($users->email, $password, $users->names . " " . $users->lastnames, $users->code);

        return $users;
    }

    public function login(LoginRequest $request)
    {
        $validated = $request->validated();

        $user = User::where('email', $request->email)->first();
        
        if ($user == NULL || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => "Usuario no encontrado. Verifique sus credenciales."
            ], 401);
        }

        // $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;

        $token->save();

        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString()
        ], 200);
    }

    public function profile(Request $request)
    {
        $user = $request->user();
        return $user;
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return response()->json([
            'message' => 'Cierre de sesión exitoso'
        ], 200);
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
            $mail->Subject = 'Confirma tu correo electrónico';
            $mail->Body    = 'Hola ' . $names . '<br>Su contraseña es: <strong>' . $password . "</strong><br><a href=\"http://localhost:8000/usuarios/confirm/$code\">Verifica tu correo electrónico</a>";

            $mail->send();
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }
}
