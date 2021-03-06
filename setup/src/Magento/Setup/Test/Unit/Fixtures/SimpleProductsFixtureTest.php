<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Test\Unit\Fixtures;

use \Magento\Setup\Fixtures\SimpleProductsFixture;

class SimpleProductsFixtureTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Setup\Fixtures\FixtureModel
     */
    private $fixtureModelMock;

    /**
     * @var \Magento\Setup\Fixtures\SimpleProductsFixture
     */
    private $model;

    public function setUp()
    {
        $this->fixtureModelMock = $this->getMock('\Magento\Setup\Fixtures\FixtureModel', [], [], '', false);

        $this->model = new SimpleProductsFixture($this->fixtureModelMock);
    }

    public function testExecute()
    {
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);
        $storeMock->expects($this->once())
            ->method('getRootCategoryId')
            ->willReturn(1);

        $websiteMock = $this->getMock('\Magento\Store\Model\Website', [], [], '', false);
        $websiteMock->expects($this->once())
            ->method('getCode')
            ->willReturn('website_code');
        $websiteMock->expects($this->once())
            ->method('getGroups')
            ->willReturn([$storeMock]);

        $storeManagerMock = $this->getMock('Magento\Store\Model\StoreManager', [], [], '', false);
        $storeManagerMock->expects($this->once())
            ->method('getWebsites')
            ->willReturn([$websiteMock]);

        $importMock = $this->getMock('\Magento\ImportExport\Model\Import', [], [], '', false);

        $contextMock = $this->getMock('\Magento\Framework\Model\ModelResource\Db\Context', [], [], '', false);
        $abstractDbMock = $this->getMockForAbstractClass(
            '\Magento\Framework\Model\ModelResource\Db\AbstractDb',
            [$contextMock],
            '',
            true,
            true,
            true,
            ['getAllChildren']
        );
        $abstractDbMock->expects($this->once())
            ->method('getAllChildren')
            ->will($this->returnValue([1]));

        $categoryMock = $this->getMock('Magento\Catalog\Model\Category', [], [], '', false);
        $categoryMock->expects($this->once())
            ->method('getResource')
            ->willReturn($abstractDbMock);
        $categoryMock->expects($this->once())
            ->method('getPath')
            ->willReturn('path/to/category');
        $categoryMock->expects($this->exactly(5))
            ->method('load')
            ->willReturnSelf();
        $categoryMock->expects($this->exactly(3))
            ->method('getName')
            ->willReturn('category_name');

        $valueMap = [
            [
                'Magento\ImportExport\Model\Import',
                [
                    'data' => [
                        'entity' => 'catalog_product',
                        'behavior' => 'append',
                        'validation_strategy' => 'validation-stop-on-errors'
                    ]
                ],
                $importMock
            ],
            ['Magento\Store\Model\StoreManager', [], $storeManagerMock]
        ];

        $objectManagerMock = $this->getMock('Magento\Framework\ObjectManager\ObjectManager', [], [], '', false);
        $objectManagerMock->expects($this->exactly(2))
            ->method('create')
            ->will($this->returnValueMap($valueMap));
        $objectManagerMock->expects($this->once())
            ->method('get')
            ->willReturn($categoryMock);

        $this->fixtureModelMock
            ->expects($this->once())
            ->method('getValue')
            ->willReturn(1);
        $this->fixtureModelMock
            ->expects($this->exactly(3))
            ->method('getObjectManager')
            ->willReturn($objectManagerMock);

        $this->model->execute();
    }

    public function testNoFixtureConfigValue()
    {
        $importMock = $this->getMock('\Magento\ImportExport\Model\Import', [], [], '', false);
        $importMock->expects($this->never())->method('validateSource');
        $importMock->expects($this->never())->method('importSource');

        $objectManagerMock = $this->getMock('Magento\Framework\ObjectManager\ObjectManager', [], [], '', false);
        $objectManagerMock->expects($this->never())
            ->method('create')
            ->with($this->equalTo('Magento\ImportExport\Model\Import'))
            ->willReturn($importMock);

        $this->fixtureModelMock
            ->expects($this->never())
            ->method('getObjectManager')
            ->will($this->returnValue($objectManagerMock));
        $this->fixtureModelMock
            ->expects($this->once())
            ->method('getValue')
            ->willReturn(false);

        $this->model->execute();
    }

    public function testGetActionTitle()
    {
        $this->assertSame('Generating simple products', $this->model->getActionTitle());
    }

    public function testIntroduceParamLabels()
    {
        $this->assertSame([
            'simple_products' => 'Simple products'
        ], $this->model->introduceParamLabels());
    }
}
