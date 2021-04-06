<?php

namespace App\Form;

use App\Constant\ThematiqueConstants;

use App\Entity\Slot;
use App\Entity\Patient;
use App\Entity\Soignant;

use Doctrine\ORM\EntityRepository;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class SlotType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date', DateType::class, array(
                'label' => 'Date',
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'attr' => [
                    'class' => 'datepicker',
                    'placeholder' => "dd/mm/yyyy"
                ],
                'html5' => false
            ))
            ->add('heureDebut', TimeType::class, array(
                'label' => 'Heure début',
                'widget' => 'single_text',
                'attr' => [
                    'placeholder' => "hh:mm"
                ],
            ))
            ->add('heureFin', TimeType::class, array(
                'label' => 'Heure début',
                'widget' => 'single_text',
                'attr' => [
                    'placeholder' => "hh:mm"
                ],
            ))
            ->add('categorie', ChoiceType::class, array(
                'label' => 'Catégorie',
                'placeholder' => '',
                'choices' => array(
                    '' => '',
                    'Entretien' => 'Entretien',
                    'Consultation' => 'Consultation',
                    'Atelier' => 'Atelier',
                    'Semaine éducative' => 'Educative',
                    'Coaching PRM' => 'Coaching'
                ),
                'choice_attr' => [
                    '' => ['class' => 'white', 'data-thematique' => ''],
                    'Entretien' => ['class' => 'rebeccapurple', 'data-thematique' => 'entretien'],
                    'Consultation' => ['class' => 'seagreen', 'data-thematique' => 'consultation'],
                    'Atelier' => ['class' => 'chocolate', 'data-thematique' => 'atelier'],
                    'Semaine éducative' => ['class' => 'royalblue', 'data-thematique' => 'educative'],
                    'Coaching PRM' => ['class' => 'goldenrod', 'data-thematique' => 'coaching']
                ],
            ))
            ->add('thematique', ChoiceType::class, array(
                'label' => 'Thématique',
                'placeholder' => '',
                'choices' => array()
            ))
            ->add('type', ChoiceType::class, array(
                'label' => 'Type',
                'placeholder' => '',
                'choices' => array(
                    '' => '',
                    'Ambu' => 'Ambu',
                    'Tel' => 'Tel',
                    'Hospit' => 'Hospit',
                )
            ))
            ->add('location', TextType::class, array(
                'label' => 'Lieu',
            ))
            ->add('place', IntegerType::class, array(
                'label' => 'Nombre de places',
            ))
            ->add('soignant', EntityType::class, array(
                'class' => Soignant::class,
                'query_builder' => function (EntityRepository $qb) {
                    return $qb->createQueryBuilder('s')
                        ->where('s.status = 1')
                        ->orderBy('s.nom', 'ASC');
                },
                'choice_label' => function(Soignant $soignant) {
                    return $soignant ? sprintf('%s %s', $soignant->getNom(), $soignant->getPrenom()) : '';
                },
                'placeholder' => '',
            ))
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
