<?php

namespace UserBundle\Security\Core\User;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider as BaseClass;
use Symfony\Component\Security\Core\User\UserInterface;

class FOSUBUserProvider extends BaseClass
{

    public function connect(UserInterface $user, UserResponseInterface $response)
    {
        $prop = $this->getProperty($response);
        $username = $response->getUsername();

        $service = $response->getResourceOwner()->getName();
        $setter = 'set'.ucfirst($service);
        $setId = $setter.'Id';
        $setToken = $setter.'AccessToken';

        if (null !== $previousUser = $this->userManager->findUserBy(array($prop => $username))) {
            $previousUser->$setId(null);
            $previousUser->$setToken(null);
            $this->userManager->updateUser($previousUser);
        }

        $user->$setId($username);
        $user->$setToken($response->getAccessToken());
        $this->userManager->updateUser($user);
    }

    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $username = $response->getUsername();
        $email = $response->getEmail();
        $password = md5(time().$username);
        $user = $this->userManager->findUserBy(array($this->getProperty($response) => $username));
        if (null === $user) {
            $service = $response->getResourceOwner()->getName();
            $setter = 'set'.ucfirst($service);
            $setId = $setter.'Id';
            $setToken = $setter.'AccessToken';
            $user = $this->userManager->createUser();
            $user->$setId($username);
            $user->$setToken($response->getAccessToken());
            $user->setUsername($username);
            $user->setEmail($email);
            $user->setPlainPassword($password);
            $user->setEnabled(true);
            $this->userManager->updateUser($user);
            return $user;
        }
        $user = parent::loadUserByOAuthUserResponse($response);
        $serviceName = $response->getResourceOwner()->getName();
        $setter = 'set' . ucfirst($serviceName) . 'AccessToken';
        $user->$setter($response->getAccessToken());
        return $user;
    }
}