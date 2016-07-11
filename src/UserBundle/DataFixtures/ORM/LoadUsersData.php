<?
namespace UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use UserBundle\Entity\User;

class LoadUserData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $contUsers = 3;
        for ($i=0; $i<$contUsers; $i++) {
            $user = new User();
            if ($i==0) {
                $user->setSuperAdmin(true);
                $user->setUsername('admin');
                $user->setPlainPassword('123');
                $user->setEmail('admin@mail.com');
                $user->setEnabled(true);
                $user->setSuperAdmin(true);
                $manager->persist($user);
                $manager->flush();
            }
            else {
                $user->setUsername('user'.$i);
                $user->setPlainPassword('user'.$i);
                $user->setEmail('user'.$i.'@mail.com');
                $user->setEnabled(true);
                $manager->persist($user);
                $manager->flush();
            }
        }
    }
}