<?php
class ControllerFeedProm extends Controller {
    protected $output;
    
public function index() {
    
        $this->load->model('catalog/category');
		$this->load->model('catalog/product');
        
    $this->output  .= '<?xml version="1.0" encoding="UTF-8"?>'."\n";
    $this->output  .= '<!DOCTYPE yml_catalog SYSTEM "shops.dtd">'."\n";
    $this->output  .= '<yml_catalog date="'.date("Y-m-d H:i").'">'."\n";
        $this->output  .= '<shop>'."\n";
            $this->output  .= '<name>' . $this->config->get('config_name') . '</name>'."\n";
            $this->output  .= '<company>' . $this->config->get('config_meta_description') . '</company>'."\n";
            $this->output  .= '<url>' . HTTP_SERVER . '</url>'."\n";
            $this->output  .= '<phone>' . $this->config->get('config_telephone') . '</phone>'."\n";
            $this->output  .= '<platform>ocStore</platform>'."\n";
            $this->output  .= '<version>2.1.0.2.1</version>'."\n";
            $this->output  .= '<currencies><currency rate="1.0000" id="UAH"/></currencies>'."\n";
            $this->output  .= '<categories>'."\n";        
        
      
                $Categories = $this->model_catalog_category->getCategories();
                foreach ($Categories as $category){
                    $this->output  .= '<category id="'.$category['category_id'].'">'.$category['name'].'</category>'."\n";
                    $childrens = $this->model_catalog_category->getCategories($category['category_id']);
                    if (!empty($childrens)){
                        self::getChildrens($childrens, $category);
                    }

                }

    
            $this->output  .= '</categories>'."\n";
            $this->output  .= '<store>true</store>
                         <pickup>true</pickup>
                         <delivery>true</delivery>'."\n";
            $this->output  .= '<offers>'."\n";
            $filter_data = array(
					'filter_filter'      => false
				);

				$products = $this->model_catalog_product->getProducts();
                $categoryId = $this->model_catalog_product->UniqueCategory();
                foreach($products as $product){
                    $this->output  .= '<offer id="'.$product['product_id'].'" available="true">'."\n";
                        $this->output  .= '<url>'.$this->url->link('product/product', 'product_id=' . $product['product_id']).'</url>'."\n";
                        $this->output  .= '<price>'.round(floatval($this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')),'UAH')), 2).'</price>'."\n";
                        $this->output  .= '<currencyId>UAH</currencyId>'."\n";
                        $this->output  .= '<categoryId type="Own">'.$categoryId[$product['product_id']].'</categoryId>'."\n";
                        $this->output  .= '<picture>http://razborkino.com.ua/image/'.$product['image'].'</picture>'."\n";
                        $this->output  .= '<name>'.$product['name'].'</name>'."\n";
                        $this->output  .= '<vendor>Renault</vendor>'."\n";
                        $this->output  .= '<description>'.$product['meta_description'].'</description>'."\n";
                    $this->output  .= '</offer> '."\n";
                }
            $this->output  .= '</offers>'."\n";             
 
            
    $this->output  .= '</shop>'."\n";
    $this->output  .= '</yml_catalog>';
    $this->response->addHeader('Content-Type: application/xml; charset=utf-8');
    $this->response->setOutput($this->output);
}


	protected function getChildrens($childrens, $category){
            foreach ($childrens as $children){
                            $this->output  .= '<category id="'.$children['category_id'].'" parentId="'.$category['category_id'].'">'.$children['name'].'</category>'."\n";
                            $childs = $this->model_catalog_category->getCategories($children['category_id']);
                            if (!empty($childs)){
                                self::getChildrens($childs, $children);
                            }
 
            }
	   
	}
    
    
}
