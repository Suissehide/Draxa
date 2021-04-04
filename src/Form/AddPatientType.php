<?php

namespace App\Form;

use App\Entity\Slot;
use App\Entity\Patient;

use Doctrine\ORM\EntityRepository;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class AddPatientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('rendezVous', EntityType::class, array(
                'label' => 'Patient',
                'class' => Patient::class,
                'query_builder' => function (EntityRepository $qb) {
                    return $qb->createQueryBuilder('p')
                        ->orderBy('p.nom', 'ASC');
                },
                'choice_label' => function(Patient $patient) {
                    return sprintf('%s %s', $patient->getNom(), $patient->getPrenom());
                },
                'placeholder' => '',
                'attr' => [
                    'class' => 'js-patient'
                ],
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Slot::class,
        ]);
    }
}
