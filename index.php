<?php

require('vendor/autoload.php');

session_start();
setcookie(session_name(),session_id(),time() + 300);

$loader = new Twig_Loader_Filesystem('src/views');
$twig = new Twig_Environment($loader);
$request = \Symfony\Component\HttpFoundation\Request::createFromGlobals();

if($request->isMethod('POST') && $request->isXmlHttpRequest()){
    $form = $request->request->all();

    if(
        $form['name'] && strlen($form['name']) > 0 &&
        $form['company'] && strlen($form['company']) > 0 &&
        $form['position'] && strlen($form['position']) > 0 &&
        $form['email'] && filter_var($form['email'],FILTER_VALIDATE_EMAIL) &&
        !isset($_SESSION['cube-sent'])
    ){
        $targetEmail = 'info@i-vengo.com';
        $_SESSION['cube-sent'] = 1;

        $transport = Swift_SmtpTransport::newInstance('smtp.gmail.com',465,'ssl')
            ->setUsername('no-reply@i-vengo.com')
            ->setPassword('inYg-_j5D5Ri-vNvgvsNF6');

        $mailer = Swift_Mailer::newInstance($transport);

        $message = Swift_Message::newInstance()
            ->setSubject('Регистрация на вечеринку')
            ->setFrom(array('no-reply@i-vengo.com'))
            ->setTo(array($targetEmail))
            ->setBody($twig->render('mail.html.twig',array(
                'name' => $form['name'],
                'company' => $form['company'],
                'position' => $form['position'],
                'email' => $form['email']
            )),'text/html');

        echo $mailer->send($message);
    }else{
        echo 2;
    }
}else{
    echo $twig->render('index.html.twig');
}





