# SortMyBooks API


Welcome to Sort My Books. This documentation will help you to get started with SortMyBooks API.

## General Considerations

SortMyBooks works with objects such as Company, Invoice, Product and so on. Each object is stored and identified by ID which is permanently assigned to your records.
Several objects in the system can have common properties. For example, Purchase and Invoice are very similar by their nature, although are defined as separate entities.

When accessing company through API, you need a couple of things:

 - Your user id should have access to API feature. This is by default available to the owner of the company if not the owner of the company can grant you this permission.
 
 - You will need to generate your API key. This key is used for identification when youare connecting through API. You must protect your key and if you think it's compromised, generate new one.
All the requests to the API will be automatically logged and presented under audit log.

## Getting API key

Get the API Key - Login into SortMyBooks account under Home/Settings you will see tab: "API".

(image)There you will be able to generate yourself a hash key as well as enable API use for your account. Hash is a substitute for both username and password therefore must be kept in secret.
## Demo
You can download a simple PHP application written in [Agile Toolkit](http://agiletoolkit.org/) which demonstrates how to use the API and integrate the system with your User Interface.- Demo: http://smbo-api-test.daisy.agile55.com
- Code: https://github.com/romaninsh/smbo-api-test

## Getting Started

Once you have enabled API, you can start exploring it by following this link:

https://sortmybooksonline.com/api/json/list. 

Once your API key is verified, it will list all the endpoints available to you. If you do not specify POST data then API will conveniently present you with a "Basic Form" to enter required values.

The URL is constructed by starting with https://sortmybooksonline.com/api/json/ and appending name of the entity you wish to work.

If you use PHP, this could be a handy class for you:

https://github.com/romaninsh/smbo-api-test/blob/master/lib/Controller/SMBO.php


## Calling JSON APIs

Each JSON call is determined by "type" and "operation". For example "type" may be "product" and "operation" might be "list".Each combination of type and operation can have it's own set of arguments. The URL is formed like this:
https://sortmybooksonline.com/api/json/product/list
Where "product" and "list" may be substituted as needed. All the additional data is passed through POST data. 
## Hard and Soft referencing
Each object has its unique ID in the system; however you can also use soft referencing. For example you can use invoice's "Reference" number instead of ID with invoices. Similarly you can use name of the product or service.
You must be aware that while Hard references can never be changed (only deleted), soft references can be changed by user.
## Standard Operations
Some operations may exist for different types, possibly with slight differences. For example, product/list is very similar to service/list. Below is the list of operations which are available for almost any type (hash means associative array):
(table)
## System selection
API can operate with multiple company data. The company is identified through field "system_id" which often is present. All the requests require "system_id" to be passed along.## Error Handling
If request cannot be processed, exception is raised. In this case, request will not return valid JSON data but will rather return an error message. Sample error message would look like this:
`Exception_JsonApi_Auth: Authentication hash is invalid.`A first token can be used to identify and classify the error, the rest is human-readable error message. Below are classifications of errors starting with `Exception_JsonApi_`
(table)
Notes:
1. Ensure that your company is using a valid credit card. If your credit card expires, APIwill stop working too.
2. API wouldn't also work until the sign-up wizard is completed properly.
## Definition of entities
When you call "describe" command, it returns list of fields and additional information as detailed in the table below:
(table)
All entities will always have "id" column. ID will never change for particular record and it is numeric.
## Definition of types
Type as described in previous table can have different values. This is further explained in the next table:
(table)
## Understanding entity types
SortMyBooks consists of many types of entities such as invoices, purchases, payments, products, vat periods, reconciliations etc. Those types are organised hierarchically. For example there is internal type "document" which is used as a base for invoices, payments, transfers etc. Invoice type is used and extended by "sales" and "purchase" invoices.
## Support
Email us your questions and suggestions on support@sortmybooks.com or better still log your ticket using https://sortmybooks.zendesk.com/forums and we will get back to you within 24hours with a response.## Future expansion of API

When enabling API, always make sure that user's email is specified correctly. When any major change will be introduced in API, all the users with enabled API will be notified.Minor changes such as addition of new types, introduction of new fields or new operations may not be announced. Your software must properly ignore unrecognized data or handle it generically.
## Sample Code:

### Adding new CustomerPHP:
```$data=array(  'hash'=>shecangetitfrom home>settings>apikey  'system_id'=>system_id,  'legal_name'=>'TestCustomer',  'vat_regis tered'=>'Y',  'job_id'=>job_id,  //See  https://sortmybooksonline.com/api/json/job/list   'currency_id'=>1,  //See https://sortmybooksonline.com/api/json/currency/list   'country_id'=>1,   //See https://sortmybooksonline.com/api/json/country/list
  'payee_id'=>5,     //1=> Supplier, 2=> Employee, 3=> Sub-Contractor, 4=> Other(supplier),
                     //5=> Customer, 6=> Principal Contractor,         8 => Other(client));/*NB: I am only adding the mandatoryfields other fields can be added as required*/
$url='https://sortmybooksonline.com/api/json/client/add';;$ch = curl_init();curl_setopt($ch,CURLOPT_URL,$url);curl_setopt($ch,CURLOPT_POST,true);
curl_setopt($ch,CURLOPT_POSTFIELDS,$data);curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);$result=curl_exec($ch);
curl_close($ch);echo "result:<pre>";print_r($result=json_decode($result,true));
```
### Adding new Invoice
Adding invoices happen in 2 steps:

 - Create Invoice Head
 - Add one or more Invoice Specs (lines)
 
For convenience I'll use the wrapper around CURL this time:

https://github.com/romaninsh/smbo-api-test/blob/master/lib/Controller/SMBO.php

```
$smbo = new Controller_SMBO();
// ^^ modify to properly include hash and system_id

$invoice_id = $smbo->call('sale','add',[
    'ref_no'=>'abc12',
    'contractor_to'=>$contractor_id,
    'due_date'=>'2019-03-20'
]);
$spec1 = $smbo->call('salespec','add',[
    'dochead_id'=>$invoice_id,
    'article_id'=>$product_id,
    'nominal_id'=>$nominal_id,
    'total_net'=>200
]);
```



