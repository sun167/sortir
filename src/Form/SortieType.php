<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToHtml5LocalDateTimeTransformer;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', null, ['label' => 'Nom de la sortie : '])
            ->add('description', null, ['label' => 'Description : '])
            ->add('dateDebut', DateTimeType::class, [
                'label' => 'Date et heure de la sortie',
                'date_widget' => 'single_text',
                'time_widget' => 'single_text',
//                'widget' => 'single_text',x`

            ])
            ->add('dateFin', DateType::class, [
                'label' => 'Cloture inscriptions ',
                'widget' => 'single_text',
            ])


            /*->add('dateFin', DateTimeType::class, ['label'=> 'fin d\'inscription'])*/
            ->add('duree', null, ['label' => 'Durée : '])
            ->add('nbInscriptionsMax', null, ['label' => 'Nombre d\'inscription max : '])
            ->add('urlPhoto', FileType::class, ['mapped' => false, 'required' => false, 'constraints' => [new Image(['maxSize' => '7024k', 'mimeTypesMessage' => "Format de l'image non supporter"])]])
            ->add('lieu', EntityType::class, ['class' => Lieu::class, 'choice_label' => 'nom'])
            ->add('campus', EntityType::class, ['class' => Campus::class, 'choice_label' => 'nom'])
            ->add('publier', SubmitType::class, ['label'=> "Publier"])
            ->add('enregistrer', SubmitType::class, ['label'=> "Enregistrer"]);
            //->add('etat', EntityType::class, ['class' => Etat::class, 'choice_label' => 'libelle']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
