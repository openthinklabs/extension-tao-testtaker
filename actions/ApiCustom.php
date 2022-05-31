<?php

/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author "Patrick Plichart, <patrick@taotesting.com>"
 *
 */

namespace oat\taoTestTaker\actions;

use oat\generis\model\GenerisRdf;
use oat\generis\model\OntologyRdf;
use oat\tao\model\routing\AnnotationReader\security;
use oat\taoTestTaker\models\CrudService;
use oat\generis\model\OntologyAwareTrait;
use oat\taoTestTaker\actions\Api;

/**
 * @deprecated
 * @see RestTestTakers
 */
class ApiCustom extends Api
{
    use OntologyAwareTrait;


    public function assign2Group() {
        $success = true ;
        try { 
            $values   = [$this->getRequestParameter('group_uri')]; //Group Resource Identifier
            $resource = $this->getResource($this->getRequestParameter('tasttaker_uri')); //Test Taker Resource Identifier
            $property = $this->getProperty('http://www.tao.lu/Ontologies/TAOGroup.rdf#member'); 
            $success = $success && $resource->editPropertyValues($property, $values);     

            return $this->returnSuccess([
                'success' => $success
            ], false);            
        } catch (Exception $e) {
            $message = $this->returnFailure($e);
          return json_encode(['success' => false, 'errorMsg' => $message]);
        }
    }

    /**
     * Search testtaker by label
     */
    public function search() {
        //harusnya menggunakan methode ini, tapi sementara kita tembak langsung databasenya
        //http://cbt.unand/taoTestTaker/TestTaker/getOntologyData?extension=taoTestTaker&perspective=TestTaker&section=manage_test_takers&classUri=http%3A%2F%2Fwww.tao.lu%2FOntologies%2FTAOSubject.rdf%23Subject&hideInstances=0&filter=*&offset=0&limit=30&selected=http_2_tao_0_local_1_mytao_0_rdf_3_i6295c0b3e603443900057a92d7e1875207
        
        try { 
        $sql = "select subject as testtaker_uri from statements where object='{$this->getRequestParameter('label')}'";
        $stmt = $this->getPersistence()->query($sql);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if(count($data) > 0) { 
            return $this->returnSuccess([
                'success' => true,
                'testtaker_uri' =>  $data['testtaker_uri']
            ], false);             
        }
        } catch (Exception $e) { 
            $message = $this->returnFailure($e);
            return json_encode(['success' => false, 'errorMsg' => $message]);               
        }
    }    
}
