<?php

namespace Application\Service;


use Base\Service\AbstractService;
use Zend\Mail\Message;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;

class MailService extends AbstractService
{
    /**
     * @param $to
     * @param $subject
     * @param $template
     * @param array $params
     */
    public function sendMail($to, $subject, $template, $params = [])
    {
        try {

            $request = $this->getServiceManager()->get('Request');
            $uri = $request->getUri();
            $scheme = $uri->getScheme();
            $host = $uri->getHost();
            $url = sprintf('%s://%s', $scheme, $host);

            $this->renderer = $this->getServiceManager()->get('ViewRenderer');
            $content = $this->renderer->render("email/{$template}", array_merge($params, ['url' => $url]));

            $html = new MimePart($content);
            $html->setType("text/html");
            $html->setCharset("UTF-8");
            $body = new MimeMessage();
            $body->setParts([$html]);

            $message = new Message();
            $message->addTo($to)
                ->addFrom('nao.responda.ortholife@ortholife.com.br')
                ->setSubject($subject)
                ->setBody($body);

            $config = $this->getServiceManager()->get('Config');

            if ($config['mail']["debug"] === true) {
                // Setup File transport
                $transport = new \Zend\Mail\Transport\File();
                $options = new \Zend\Mail\Transport\FileOptions($config['mail']['file_options']);
                $transport->setOptions($options);
            } else {
                /** @var SmtpTransport $transport */
                $transport = new SmtpTransport();

                $options = new SmtpOptions($config['smtp_options']);
                $transport->setOptions($options);
            }

            return $transport->send($message);
        } catch (\Exception $e) {
            throw new \UnexpectedValueException('Erro no serviço de envio de e-mails!' . $e->getMessage());
        }
    }



}
