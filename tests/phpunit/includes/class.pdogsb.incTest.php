<?php

/**
 * Generated by PHPUnit_SkeletonGenerator on 2017-10-10 at 22:10:25.
 */
class PdoGsbTest extends PHPUnit\Framework\TestCase {

    /**
     * @var PdoGsb
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->object = new PdoGsbTest;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
        
    }

    /**
     * @covers PdoGsb::__destruct
     * @todo   Implement test__destruct().
     */
    public function test__destruct() {
        // Remove the following lines when you implement this test.
        $this->assertEquals(1, 0);
    }

    /**
     * @covers PdoGsb::getPdoGsb
     * @todo   Implement testGetPdoGsb().
     */
    public function testGetPdoGsb() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers PdoGsb::getInfosVisiteur
     * @todo   Implement testGetInfosVisiteur().
     */
    public function testGetInfosVisiteur() {
      $array = ["id" => "a131", "nom" => "Villechalane", "prenom" => "Louis"];
      $this->assertSame($array, getInfosVisiteur('lvillachane', 'ju7xg'));
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers PdoGsb::getLesFraisHorsForfait
     * @todo   Implement testGetLesFraisHorsForfait().
     */
    public function testGetLesFraisHorsForfait() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers PdoGsb::getNbjustificatifs
     * @todo   Implement testGetNbjustificatifs().
     */
    public function testGetNbjustificatifs() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers PdoGsb::getLesFraisForfait
     * @todo   Implement testGetLesFraisForfait().
     */
    public function testGetLesFraisForfait() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers PdoGsb::getLesIdFrais
     * @todo   Implement testGetLesIdFrais().
     */
    public function testGetLesIdFrais() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers PdoGsb::majFraisForfait
     * @todo   Implement testMajFraisForfait().
     */
    public function testMajFraisForfait() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers PdoGsb::majNbJustificatifs
     * @todo   Implement testMajNbJustificatifs().
     */
    public function testMajNbJustificatifs() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers PdoGsb::estPremierFraisMois
     * @todo   Implement testEstPremierFraisMois().
     */
    public function testEstPremierFraisMois() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers PdoGsb::dernierMoisSaisi
     * @todo   Implement testDernierMoisSaisi().
     */
    public function testDernierMoisSaisi() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers PdoGsb::creeNouvellesLignesFrais
     * @todo   Implement testCreeNouvellesLignesFrais().
     */
    public function testCreeNouvellesLignesFrais() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers PdoGsb::creeNouveauFraisHorsForfait
     * @todo   Implement testCreeNouveauFraisHorsForfait().
     */
    public function testCreeNouveauFraisHorsForfait() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers PdoGsb::supprimerFraisHorsForfait
     * @todo   Implement testSupprimerFraisHorsForfait().
     */
    public function testSupprimerFraisHorsForfait() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers PdoGsb::getLesMoisDisponibles
     * @todo   Implement testGetLesMoisDisponibles().
     */
    public function testGetLesMoisDisponibles() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers PdoGsb::getLesInfosFicheFrais
     * @todo   Implement testGetLesInfosFicheFrais().
     */
    public function testGetLesInfosFicheFrais() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers PdoGsb::majEtatFicheFrais
     * @todo   Implement testMajEtatFicheFrais().
     */
    public function testMajEtatFicheFrais() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

}
