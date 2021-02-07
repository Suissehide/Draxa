<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Patient;

class PatientFixtures extends Fixture
{
	public function load(ObjectManager $manager)
    {
        $patients = array(
            'patient1' => ['Observ1', 'Absence besoins éducatifs', 'DE', new \DateTime('2018-07-03'), 'Léo', 'Couffinhal', '05 56 76 26 10', '', 'homme', new \DateTime('1997-03-12'), 'Gironde', 'universitaire', 'Professions intermédiaires', 'actif', 'AVC', new \DateTime('2018-07-03'), 'Orientation pro santé au cours hospit', 'non', '', 'HRCV', '', ''],
            'patient2' => ['Observ2', 'Absence besoins éducatifs', 'DE', new \DateTime('2018-06-03'), 'Téo', 'Laulan', '05 86 86 93 48', '', 'homme', new \DateTime('1996-09-22'), 'Gironde', 'secondaire', 'Cadres et professions intellectuelles supérieures', 'retraité', 'AVC', new \DateTime('2018-07-03'), '', 'non', 'Absence de besoin', 'HRCV', '', ''],
            'patient3' => ['Observ3', 'Absence besoins éducatifs', 'M12', new \DateTime('2018-07-04'), 'Kevin', 'Bon', '06 87 12 23 29', ' 05 56 20 21 45', 'femme', new \DateTime('2001-05-03'), 'CUB', 'universitaire', 'Employés', 'retraité', 'CORONAROPATHIE', new \DateTime('2018-07-03'), '', 'oui', '', 'HRCV', '', ''],
            'patient4' => ['Observ4', 'Absence besoins éducatifs', 'DE', new \DateTime('2018-08-13'), 'Boris', 'Lomakin', '06 80 50 53 10', '', 'homme', new \DateTime('1999-11-16'), 'Hors Gironde', 'primaire', 'Artisans, commerçants et chefs d\'entreprises', 'actif', 'AVC', new \DateTime('2018-07-03'), '', 'non', 'Bon contrôle FDR', 'HRCV', '', ''],
        );
        foreach ($patients as $a => $value) {
            $patient = new Patient();
            $patient->setObserv($value[0]);
            $patient->setMotif($value[1]);
            $patient->setEtp($value[2]);
            $patient->setDate($value[3]);
            $patient->setPrenom($value[4]);
            $patient->setNom($value[5]);
            $patient->setTel1($value[6]);
            $patient->setTel2($value[7]);
            $patient->setSexe($value[8]);
            $patient->setDentree($value[9]);
            $patient->setDistance($value[10]);
            $patient->setEtude($value[11]);
            $patient->setProfession($value[12]);
            $patient->setActivite($value[13]);
            $patient->setDiagnostic($value[14]);
            $patient->setDedate($value[15]);
            $patient->setOrientation($value[16]);
            $patient->setEtpdecision($value[17]);
            $patient->setPrecisions($value[18]);
            $patient->setProgetp($value[19]);
            $patient->setPrecisionsperso($value[20]);
            $patient->setObjectif($value[21]);
            $manager->persist($patient);
        }
        $manager->flush();
    }
}
