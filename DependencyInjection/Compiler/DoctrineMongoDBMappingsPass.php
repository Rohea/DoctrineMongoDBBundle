<?php

/*
 * This file is part of the Doctrine Bundle
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 * (c) Doctrine Project, Benjamin Eberlei <kontakt@beberlei.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Doctrine\Bundle\MongoDBBundle\DependencyInjection\Compiler;

use Symfony\Bridge\Doctrine\DependencyInjection\CompilerPass\RegisterMappingsPass as BaseMappingPass;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class for Symfony bundles to configure mappings for model classes not in the
 * automapped folder.
 *
 * @author David Buchmann <david@liip.ch>
 */
class DoctrineMongoDBMappingsPass extends BaseMappingPass
{
    /**
     * You should not directly instantiate this class but use one of the
     * factory methods.
     *
     * @param Definition|Reference $driver           the driver to use
     * @param array                $namespaces       list of namespaces this driver should handle
     * @param bool                 $enabledParameter if specified, the compiler pass only
     *      executes if this parameter exists in the service container.
     */
    public function __construct($driver, $namespaces, $enabledParameter = false)
    {
        parent::__construct(
            $driver,
            $namespaces,
            'doctrine_mongodb.odm.document_managers',
            'doctrine_mongodb.odm.%s_metadata_driver',
            $enabledParameter
        );

    }

    /**
     * @param array  $mappings         Hashmap of directory path to namespace
     * @param string $enabledParameter Service container parameter that must be
     *      present to enable the mapping. Set to false to not do any check, optional.
     */
    public static function createXmlMappingDriver(array $mappings, $enabledParameter = false)
    {
        $arguments = array($mappings, '.mongodb.xml');
        $locator = new Definition('Doctrine\Common\Persistence\Mapping\Driver\SymfonyFileLocator', $arguments);
        $driver = new Definition('Doctrine\ODM\MongoDB\Mapping\Driver\XmlDriver', array($locator));

        return new DoctrineMongoDBMappingsPass($driver, $mappings, $enabledParameter);
    }

    /**
     * @param array  $mappings         Hashmap of directory path to namespace
     * @param string $enabledParameter Service container parameter that must be
     *      present to enable the mapping. Set to false to not do any check, optional.
     */
    public static function createYamlMappingDriver(array $mappings, $enabledParameter = false)
    {
        $arguments = array($mappings, '.mongodb.yml');
        $locator = new Definition('Doctrine\Common\Persistence\Mapping\Driver\SymfonyFileLocator', $arguments);
        $driver = new Definition('Doctrine\ODM\MongoDB\Mapping\Driver\YamlDriver', array($locator));

        return new DoctrineMongoDBMappingsPass($driver, $mappings, $enabledParameter);
    }

    /**
     * @param array  $namespaces       List of namespaces that are handled with annotation mapping
     * @param array  $directories      List of directories to look for annotation mapping files
     * @param string $enabledParameter Service container parameter that must be
     *      present to enable the mapping. Set to false to not do any check, optional.
     */
    public static function createAnnotationMappingDriver(array $namespaces, array $directories, $enabledParameter = false)
    {
        $arguments = array(new Reference('doctrine_mongodb.odm.metadata.annotation_reader'), $directories);
        $locator = new Definition('Doctrine\Common\Persistence\Mapping\Driver\SymfonyFileLocator', $arguments);
        $driver = new Definition('Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver', array($locator));

        return new DoctrineMongoDBMappingsPass($driver, $namespaces, $enabledParameter);
    }
}
