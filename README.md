# Data Extract Bundle #
Symfony2 Bundle to assist with (meta)data extraction from entities. Data can be
extracted from Doctrine entities using annotations, data from other objects can
be extracted using tagged services. When parsing entities and tagged services
a dictionary is build that can be used to inform the user which data is available.

## Author ##
This Symfony2 bundle is provided for free by [VisualMedia](http://www.visualmedia.nl) a dutch web development, web design and internet marketing company. 

## Feature overview ##
* Available data is configured using annotations
* Tagged service interface for extra (not annotated) data
* Dictionary with available data

## Requirements ##
* PHP 5.4+
* Symfony v2.4+
* Doctrine ORM v1.2+
* Doctrine Bundle v2.4+


## Use Case ##
The content in every application contains valuable information that can be used
in (meta)tags, search indexers, etc. This information can be link within program
code, but this prevents marketeers to use the information without stepping to a
developer. Within our application we use an easy to use expression language that
has access to the extracted data, enabling marketeers to create new and dynamic
tags based on information contained in the application. The dictionary shows them
which information is available.

## Design considirations ##

### No MetaData inheritance ###
When scanning for available (meta)data only the entity classes are considered,
this is by design and prevents unintentional inheritance for non relevant data.
In the future a new annotation could be added that indicates how deep the system
should scan for annotations

### Only annotations based ###
At the moment only annotations are used for providing meta(data) configuration,
this is done becouse VisualMedia works with annotations for configuring columns,
etc. XML and/or YAML configuration files could be implemented.

### No global (meta)data ###
The bundle only extracts (meta)data from objects and has no way to provide global
(meta)data like an analytics code eg. This is by design as this is a whole new
use case.

## Installation ##

Add the following dependencies to your projects composer.json file:

    "require": {
        # ..
        "visualmedia/data_extract_bundle": "~1.0"
        # ..
    }

Enable VisualMDataExtractBundle in your AppKernel:

	
	<?php
	// app/AppKernel.php
	
	public function registerBundles()
	{
	    $bundles = array(
	        // ...
	        new VisualM\DataExtractBundle\VisualMDataExtractBundle(),
	    );
	}

## Usage ##

### Add Annotations to Doctrine Entities ###
Trough this annotations the Bundle (DictionaryCollector) can scan for available (meta)data in Doctrine Entities 

	<?php
	
	use VisualM\DataExtractBundle\Annotation\DataElement;
	
	// ...
	
	/** 
	 * @ORM\Entity()
	 * 
	 * @DataElement(fields="title, page.title", getter="getTitle")
	 * @DataElement(fields="meta_description, page.meta_description", getter="getMetaDescription")
	 * @DataElement(fields="page.changed", getter="getChanged", type="datetime")
	 * @DataElement(fields="page.changed", getter="getChangedFormatted")
	 */
	class Page
	{
	
	// ...

### Use the DictionaryCollector to list available fields ###
With the DictionaryCollector it is possible to list all fields that are provided by Entities and DataProviders (see Advanced usage section). This list can be used to show the user a list of available fields.

	<?php

	$dictionary_collector = $this->container->get('visualm.data.dictionary_collector');

	// List all available field	
	var_dump($dict_collector->getAvailableFields());

	// Returns:
	//
	// array
	//   0 => string 'meta_description'
	//   1 => string 'title'
	//   2 => string 'page.changed'
	//   3 => string 'page.meta_description'
	//   4 => string 'page.title'

	// List fields of specific type(s)
	var_dump($dict_collector->getAvailableFields([ TypeEnum::DATETIME ]));

	// Returns:
	//
	// array
	//   0 => string 'page.changed'

### Get (meta)data for a specific object/entity ###
With the DataCollector it is possible to extract data from a specific object/entitiy without storing it into the DataCollector. When retrieving (meta)data it is possible to indicate which data can be handled (default is only STRING). When indicating more then one type, the order determines which data is returned when multiple types of data are available

	<?php

	$page = $page_repository->getPageBySlug('test');

	$data_collector = $this->get('visualm.data.data_collector');

	// Get all data from single entity of type DATETIME and STRING, prevering DATETIME
	$data = $data_collector->getForSingleObject($page, [ TypeEnum::DATETIME, TypeEnum::STRING ]);
    var_dump($data);

	// Returns:
	// array
	//   'title' => string 'Test'
	//   'page' => 
	//     array
	//       'title' => string 'Test'
	//       'changed' => 
	//         object(DateTime)[617]
	//           public 'date' => string '2014-08-01 12:00:00'
	//           public 'timezone_type' => int 3
	//           public 'timezone' => string 'Europe/Berlin'

	// Get all data from single entity of type DATETIME and STRING, prevering STRING
	$data = $data_collector->getForSingleObject($page, [ TypeEnum::STRING, TypeEnum::DATETIME ]);
    var_dump($data);

	// Returns:
	// array
	//   'title' => string 'Test'
	//   'page' => 
	//     array
	//       'title' => string 'Test'
	//       'changed' => string '2014-08-01 12:00:00'
      
### Get (meta)data for multiple objects/entities ###
With the DataCollector it is possible to extract data from multiple objects/entities, the DataCollector remembers which data it extracted from the different objects and allows collection of one aggregrated set. When retrieving (meta)data it is possible to indicate which data can be handled (default is only STRING). When indicating more then one type, the order determines which data is returned when multiple types of data are available. 


	<?php

	$page = $page_repository->getPageBySlug('test');
	$page2 = $page_repository->getPageBySlug('test2');	

	$data_collector = $this->get('visualm.data.data_collector');

	// Push objects into collector
	$data_collector->collectObject($page);
	$data_collector->collectObject($page2);

	// Get all collected data of type DATETIME and STRING, prevering DATETIME
	$data = $data_collector->getCollected($page, [ TypeEnum::DATETIME, TypeEnum::STRING ]);
    var_dump($data);

	// Returns:
	// array
	//   'title' => string 'Test2'
	//   'page' => 
	//     array
	//       'title' => string 'Test2'
	//       'changed' => 
	//         object(DateTime)[617]
	//           public 'date' => string '2014-08-01 12:00:00'
	//           public 'timezone_type' => int 3
	//           public 'timezone' => string 'Europe/Berlin'

	// Get all collected data of type DATETIME and STRING, prevering STRING
	$data = $data_collector->getCollected($page, [ TypeEnum::STRING, TypeEnum::DATETIME ]);
    var_dump($data);

	// Returns:
	// array
	//   'title' => string 'Test2'
	//   'page' => 
	//     array
	//       'title' => string 'Test2'
	//       'changed' => string '2014-08-01 12:00:00'

## Advanced Usage ##

### DataProviderInterface ###
A DataProvider can be used to extract data from object that cannot be provided using the annotation system, for instance becouse it uses an service not available within the entity (say Routing)

#### Create the DataProvider class ####
A DataProvider must implement the DataProviderInterface. This Interface requires two functions, the first function getProvidedFields() indicates to the DictionaryCollector which fields it provides. The second function extractData($object) is responsible for extracting the data from the object. This function is responsible for checking if it can handle the object (using instanceOf for example).

	<?php

	use VisualM\DataExtractBundle\Data\CollectedData;
	use VisualM\DataExtractBundle\Data\ProvidedField;
	use VisualM\DataExtractBundle\Data\TypeEnum;
	use VisualM\DataExtractBundle\Provider\DataProviderInterface;

	// ...
	
	class SlugDataProvider implements DataProviderInterface
	{
        /*
         * {@inheritDoc}
         */
	    public function extractData($object)
	    {
	        if ($object instanceof Page) {            
	            return [
	                (new CollectedData('slug'))->addData(TypeEnum::STRING, $object->getSlug()),
	                (new CollectedData('page.slug'))->addData(TypeEnum::STRING, $object->getSlug())
	            ];
	        }        
	        return [];
	    }

        /*
         * {@inheritDoc}
         */	
	    public function getProvidedFields()
	    {
	        return [
	            new ProvidedField('slug', TypeEnum::STRING),
	            new ProvidedField('page.slug', TypeEnum::STRING)
	        ];
	    }
	
	}

#### Add DataProvider to the container as tagged service ####
The Bundle scans for services with the *visualm.data.data_provider* tag and automaticly registers them with the DictionaryCollector for usage. When using the tag an optional attribute cacheable indicates if the fields that are returned by getProvidedFields() are cachable, the default is true.

	# ... 
	services:	
	# ... 
	    project.slug_data_provider:
	        class: '...\...\SlugDataProvider'
	        tags:
	            - { name: visualm.data.data_provider, cachable : true }
	# ... 

