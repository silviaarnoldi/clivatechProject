<?php
include "connessione.php";
session_start();

$username=$_POST['username'];
$password=$_POST['password'];

$password=MD5($password);

try{
        $verifica="select * from UTENTE where USERNAME='$username' and PASSWORD='$password';";
        $result=$connessione->query($verifica);
        // echo("risultat".$result->num_rows);
        if($result->num_rows>0){
            // echo("risultat".$result->num_rows);
            while($user=$result->fetch_array(MYSQLI_ASSOC)){
                // $user=$result->fetch_row();
                // echo("id:".$user['ID']);
                $id=$user['ID'];
                $username=$user['USERNAME'];
                $nome=$user['NOME'];
                $cognome=$user['COGNOME'];
                $_SESSION['id']=$id;
                $_SESSION['nome']=$nome;
                $_SESSION['cognome']=$cognome;
            }
            $result->close();
                   header("Location: home.php");
        }else{
            header("Location: login.php?err=Utente non trovato");
        }
    } catch (Exception $e) {
        // L'utente non esiste, quindi reindirizza alla pagina di login
        header("Location: login.php?err=Utente non trovato");
}
?>