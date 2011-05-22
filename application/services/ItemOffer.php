<?php

class Application_Service_ItemOffer_HandlingCriteria extends Application_Model_Base
{
		public 		$_minmatchscore 				= 70;
		public		$_mindistanceratio			= 0.3;
		public 		$_createitemonnomatch		= false;
}

class Application_Service_ItemOffer extends Application_Service_Base
{
		protected $offerDao 		= null;
		protected $itemOfferDao	= null;
		protected $itemDao 			= null;
	
		protected function __construct() {
    	$this->offerDao 		= new Application_Model_Mapper_Offer();
    	$this->itemOfferDao	= new Application_Model_Mapper_ItemOffer();
    	$this->itemDao 			= new Application_Model_Mapper_Item();
    }
    
    public static function getInstance() {
        return parent::getInstance(get_class());
    }
    
		public function addItemOffer(
			Application_Model_Item 													&$item, 
			Application_Model_Offer													&$offer,
			Application_Service_ItemOffer_HandlingCriteria	$criteria,
			$confidence = 100,
			$timestamp = 0) 
		{
			$matcher 	= WebXtractor_Matcher::getInstance();
			$mSrc 		= $matcher->compile($offer->getName());
			
			Zend_Registry::get('log')->debug("MATCHING " . join(' ', $mSrc));
			
			$resItems = $this->itemDao->getChildren($item);
			if (is_array($resItems) && count($resItems)) {
				$aItemByScore = array();
				foreach($resItems as $resItem) {
					$score = $matcher->match(
						$mSrc, 
						$matcher->compile($resItem->getName()),
						$criteria->getMindistanceratio());
					
					if ($score > $criteria->getMinmatchscore()) {
						$aItemByScore[$score . '-' . $resItem->getId()] = $resItem;
					}
				}
				
				if (0 == count($aItemByScore)) {
					return 0;
				}
				
				$nAdditions = 0;
				arsort($aItemByScore);
				foreach($aItemByScore as $score => $resItem) {
					if (($i = strpos($score, '-')) > 0) {
						$score = substr($score, 0, $i);
					}
					$nAdditions += $this->addItemOffer($resItem, $offer, $criteria, $score, $timestamp);
					break;
				}
				return $nAdditions;
			}
			
			$resItemOffer = $this->itemOfferDao->find(array(
				'iditem' 	=> $item->getId(),
				'idoffer'	=> $offer->getId()));
			$shown	= (0 == count($resItemOffer)) ? 0 : $resItemOffer[0]->getShown();
			$rank		= (0 == count($resItemOffer)) ? 0 : $resItemOffer[0]->getRank();
			
			$this->saveItemOffer($item, $offer, $confidence, $shown, $rank, $timestamp);
			return 1;
			
			/* todo: leaf criteria: such as but only if image width > W or title contains XX
			$score = $matcher->match(
				$mSrc, 
				$matcher->compile($item->getName()));
			
			if ($score > $criteria->getMinmatchscore()) {
				$this->saveItemOffer($item, $offer, $score);
			}
			*/
		}
		
		public function saveItemOffer(
			$mixedItem,
			$mixedOffer,
			$confidence, $shown = 0, $rank = 0, $timestamp = 0) 
		{
			if ($mixedItem instanceof Application_Model_Item) {
    		$item = $mixedItem;
    	} else if (is_numeric($mixedItem)) {
    		if (is_null($this->itemDao)) $this->itemDao = new Application_Model_Mapper_Item();
    		$item = $this->itemDao->get($mixedItem);
    	} else {
    		throw new Exception ('Not a valid item identifier');
    	}
    	
    	if ($mixedOffer instanceof Application_Model_Offer) {
    		$offer = $mixedOffer;
    	} else if (is_numeric($mixedOffer)) {
    		if (is_null($this->offerDao)) $this->offerDao = new Application_Model_Mapper_Offer();
    		$offer = $this->offerDao->get($mixedOffer);
    	} else {
    		throw new Exception ('Not a valid offer identifier');
    	}
    	
			if (!$item->getId()) {
				throw new Exception('item not known');
			}
			if (!$offer->getId()) {
				throw new Exception('offer not known');
			}

			Zend_Registry::get('log')->debug("SAVE OFFER ({$confidence}, {$shown}, {$rank}) " . $offer->getName() . " TO " . $item->getName());
			
			$itemOffer = new Application_Model_ItemOffer();
			$itemOffer->setIditem($item->getId());
			$itemOffer->setIdoffer($offer->getId());
				
			$resItemOffer = $this->itemOfferDao->find(array(
				'iditem' 	=> $item->getId(),
				'idoffer'	=> $offer->getId()));
			if (0 == count($resItemOffer)) {
				$itemOffer->setCreated($timestamp ? $timestamp : time());
				$itemOffer->setUpdated($timestamp ? $timestamp : time());
			} else {
				$itemOffer->setCreated($resItemOffer[0]->getCreated());
				$itemOffer->setUpdated($timestamp ? $timestamp : $resItemOffer[0]->getUpdated());
			}
			$itemOffer->setConfidence($confidence);
			$itemOffer->setShown($shown ? $shown : 0);
			$itemOffer->setRank($rank ? $rank : 0);
			
			$this->itemOfferDao->save($itemOffer);
			
			$itemOffers = $item->getItemOffers();
			if (!is_array($itemOffers)) $itemOffers = array();
			$exists = false;
			foreach($itemOffers as $idx => $existingItemOffer) {
				if ($existingItemOffer->getId() == $itemOffer->getId()) {
					$itemOffers[$idx] = $itemOffer;
					$exists = true;
				}
			}
			if (!$exists) {
				$itemOffers[] = $itemOffer;
			}
			$item->setItemOffers($itemOffers);
		}
		
		public function getHandlingCriteriaForDatasource(
			Application_Model_Item 				&$item, 
			Application_Model_Datasource	&$datasource) 
		{
			$item->setItemDatasources($this->itemDao->getItemDatasources($item));
			
			foreach($item->itemDatasources as $itemDatasource) {
				if ($datasource->getId() == $itemDatasource->getIddatasource()) {
					$handlingCriteria = new Application_Service_ItemOffer_HandlingCriteria();
    			$handlingCriteria->setMinmatchscore($itemDatasource->getMinmatchscore());
    			$handlingCriteria->setCreateitemonnomatch($itemDatasource->getCreateitemonnomatch());
					return $handlingCriteria;
				}
			}
			
			return null;
		}
}
?>