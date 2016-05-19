<?php
/*
   Commonly you would want to re-define ApiFrontend for your own application.
 */
class Frontend extends App_Frontend {
	function init(){
		parent::init();
        $this->add('jUI');


		//$this->layout->menu->addItem('Back','index');
	}


    function page_index($page){

        $t=$this->add('Tabs');
        $tt=$t->addTab('Connectivity');

        $t->addTabURL('companies');
        $t->addTabURL('invoice');



        $tt->add('H1')->set('Sortmybooks API testing suite');
        $form=$tt->add('TestForm');
        $form->addField('line','hash','API key')->set($this->api->recall('hash'));
        $form->addSubmit('Send');
        $form->onSubmit(function($form){
            $ret=$form->call('test','',$form->get());
            $form->api->memorize('hash',$form->get('hash'));
            $form->execute();
        });
    }

    function page_companies($page){
        $grid=$page->add('TestGrid');
        $grid->addColumn('text','id');
        $grid->addColumn('text','legal_name');
        $grid->addColumn('text','company_type');
        $grid->addColumn('text','accounting_year_start');
        $grid->addColumn('button','select');
        $grid->call('company','list');
        if($_GET['select']){
            $page->api->memorize('system_id',$_GET['select']);
            $page->js()->univ()->dialogOK('API Authentication successful')->execute();
        }
    }

    function page_invoice($page){
        $client = $this->add('Controller_SMBO')->get('client');
        $nominal = $this->add('Controller_SMBO')->get('nominal');
        $product = $this->add('Controller_SMBO')->get('product');
        $service = $this->add('Controller_SMBO')->get('service');


        $form=$page->add('TestForm');
        $form->addField('line','ref_no');
        $form->addField('dropdown','contractor_to')
            ->setValueList($client);

        $form->addField('dropdown','nominal1')
            ->setValueList($nominal);
        $form->addField('dropdown','item1')
            ->setValueList($product);
        $form->addField('line','qty1');
        $form->addField('line','text1');

        $form->addField('dropdown','nominal2')
            ->setValueList($nominal);
        $form->addField('dropdown','item2')
            ->setValueList($service);
        $form->addField('line','qty2');
        $form->addField('line','text2');

        $form->addSubmit('Send');
        $form->onSubmit(function($form){
            $invoice_id=$form->call('sale','add',
                array_merge($form->get(),array(
                        'due_date'=>date('Y-m-d',strtotime('+1 month'))
                    )));
            $invoice_id=$form->call('salespec','add',
                    array(
                        'dochead_id'=>$invoice_id,
                        'article_id'=>$form['item1'],
                        'nominal_id'=>$form['nominal1'],
                        'total_net'=>100,
                        'total_vat'=>21,
                        'total_gross'=>121,
                        'qty'=>$form->get('qty1'),
                        ));
            $invoice_id=$form->call('salespec','add',
                    array(
                        'dochead_id'=>$invoice_id,
                        'article_id'=>$form['item2'],
                        'nominal_id'=>$form['nominal1'],
                        'total_net'=>123,
                        'qty'=>$form->get('qty2'),
                        ));

            $form->execute();
        });
    }


}
class TestGrid extends Grid {
    function call($ctl,$command,$args=array()){
        //$res=$this->add('Controller_SMBO')->call($ctl,'describe',$args);

        $res=$this->add('Controller_SMBO')->call($ctl,$command,$args);

        $data=json_decode($res,true);
        if(is_null($data)){
            $this->owner->add('View_Error')->set($res);
            $this->setStaticSource(array());
            return;
        }

        $this->setSource($data['response']);
    }
}
class TestForm extends Form {
    public $response;
    function init(){
        parent::init();
        $this->addClass('atk-push');
        $this->owner->add('H3')->set('Response from API');
        $v = $this->owner->add('View')->addClass('atk-box-small atk-effect-info');
        $this->response=$v->add('HtmlElement')->setElement('code')->set('Ready');
    }
    function call($ctl,$command,$args){
        $res=$this->add('Controller_SMBO')->call($ctl,$command,$args);


        $this->chain=$this->response->js()->html($res);

        $data=json_decode($res,true);
        if(is_null($data)){
            $this->execute();
        }

        return $data['response'];
    }
    function execute(){
        $this->chain->execute();
    }
}
