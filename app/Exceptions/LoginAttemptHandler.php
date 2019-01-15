<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 1/15/2019
 * Time: 11:25 PM
 */

namespace App\Exceptions;


class LoginAttemptHandler
{
    protected $em;
    protected $login_attempt_minutes;
    protected $max_login_attempts;

    /**
     * Constructor.
     *
     * @param \Doctrine\ORM\EntityManager $em
     */
    public function __construct(EntityManager $em, $login_attempt_minutes, $max_login_attempts)
    {
        $this->em = $em;
        $this->max_login_attempts = $max_login_attempts;
        $this->login_attempt_minutes = $login_attempt_minutes;
    }

    /**
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @throws \Exception
     */
    public function log(Request $request)
    {
        try {

            $conn->insert('login_attempts', [
                'card_id' => $request->getClientIp()
            ]);

        }
        catch(\Exception $e) {
            throw $e;
        }
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @throws \Exception
     */
    public function clear(Request $request)
    {
        try {
            $sql = "DELETE FROM login_attempts WHERE ip_addr = :ip_addr";

            $params = array('ip_addr' => $request->getClientIp());
            $stmt   = $this->em->getConnection()->prepare($sql);
            $stmt->execute($params);
        }
        catch(\Exception $e) {
            throw $e;
        }
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return mixed
     * @throws \Exception
     */
    public function get(Request $request)
    {
        try {

            /**
             * If after three attempts you've still failed, you'll have to wait
             * 15 minutes to attempt to login again..
             */
            $conn = $this->em->getConnection();
            $sql = 'SELECT * FROM login_attempts WHERE ip_addr = :ip_addr';
            $params = array('ip_addr' => $request->getClientIp());
            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            $attempts = $stmt->fetchAll();

            return $attempts;

        }
        catch(\Exception $e) {
            throw $e;
        }
    }

    /**
     * @param array $attempts
     *
     * @return int|string
     * @throws \Exception
     */
    public function getLockoutMinutes(array $attempts = array())
    {
        try {

            $now = new \DateTime();
            $now ->setTimezone(new \DateTimeZone($this->container->getParameter('timezone')));
            $minutes = 0;

            foreach($attempts as $attempt) {
                $timestamp = $attempt['login_timestamp'];
                $loginAttempt = new \DateTime($timestamp);
                $interval = $now->diff($loginAttempt);
                $minutes = $interval->format('%i');
            }

            return $minutes;

        }
        catch(\Exception $e) {
            throw $e;
        }
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return bool
     * @throws \Exception
     */
    public function lock(Request $request)
    {
        try {

            $attempts = $this->get($request);
            $minutes = $this->getLockoutMinutes($attempts);
            return (count($attempts) >= 3 && $minutes <= $this->login_attempt_minutes) ? true : false;

        }
        catch(\Exception $e) {
            throw $e;
        }
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return bool
     * @throws \Exception
     */
    public function unlock(Request $request)
    {
        try {

            $attempts = $this->get($request);
            $minutes = $this->getLockoutMinutes($attempts);
            return (count($attempts) > $this->max_login_attempts && $minutes > $this->login_attempt_minutes) ? true : false;

        }
        catch(\Exception $e) {
            throw $e;
        }
    }

}