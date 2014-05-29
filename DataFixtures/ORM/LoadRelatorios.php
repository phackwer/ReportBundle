<?php

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SanSIS\Sgp\CoreBundle\Entity\Relatorio;

class LoadRelatorios implements FixtureInterface{

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    function load(ObjectManager $manager)
    {
        $entityRelatorio = $manager->find('SanSIS\Sgp\CoreBundle\Entity\Relatorio',1);

        if (empty($entityRelatorio)) {
            $entityRelatorio = new Relatorio();
            $entityRelatorio->setCoSgpRelatorio(1);
        }

        $entityRelatorio->setDsRelatorio('Ajuda de Custo');
        $entityRelatorio->setDsArquivoFormulario('SanSISSgpMaisMedicosBundle:Report:ajudaDeCustoForm.html.twig');
        $entityRelatorio->setDsClasseFormulario('SanSIS_sgp_mais_medicos.ajudaDeCusto');
        $entityRelatorio->setDsMetodoFormulario('paramsAjudaDeCustoForm');
        $entityRelatorio->setDsArquivoRelatorio('SanSISCoreReportBundle::reportHtml.html.twig');
        $entityRelatorio->setDsClasseRelatorio('SanSIS_sgp_mais_medicos.ajudaDeCusto');
        $entityRelatorio->setDsMetodoRelatorio('ajudaDeCusto');

        $manager->persist($entityRelatorio);
        $manager->flush();

    }
}