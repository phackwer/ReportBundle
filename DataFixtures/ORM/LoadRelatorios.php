<?php

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Ibram\Sgp\CoreBundle\Entity\Relatorio;

class LoadRelatorios implements FixtureInterface{

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    function load(ObjectManager $manager)
    {
        $entityRelatorio = $manager->find('Ibram\Sgp\CoreBundle\Entity\Relatorio',1);

        if (empty($entityRelatorio)) {
            $entityRelatorio = new Relatorio();
            $entityRelatorio->setCoSgpRelatorio(1);
        }

        $entityRelatorio->setDsRelatorio('Ajuda de Custo');
        $entityRelatorio->setDsArquivoFormulario('IbramSgpMaisMedicosBundle:Report:ajudaDeCustoForm.html.twig');
        $entityRelatorio->setDsClasseFormulario('Ibram_sgp_mais_medicos.ajudaDeCusto');
        $entityRelatorio->setDsMetodoFormulario('paramsAjudaDeCustoForm');
        $entityRelatorio->setDsArquivoRelatorio('IbramCoreReportBundle::reportHtml.html.twig');
        $entityRelatorio->setDsClasseRelatorio('Ibram_sgp_mais_medicos.ajudaDeCusto');
        $entityRelatorio->setDsMetodoRelatorio('ajudaDeCusto');

        $manager->persist($entityRelatorio);
        $manager->flush();

    }
}