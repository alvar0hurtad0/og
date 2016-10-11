<?php

namespace Drupal\Tests\og\Kernel\Console;

use Drupal\KernelTests\KernelTestBase;
use Drupal\node\Entity\NodeType;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Helper\HelperSet;

/**
 * Tests create membership helper function.
 *
 * @group og
 * @coversDefaultClass \Drupal\og\Og
 */
class DrupalConsoleAddFieldTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'entity_test',
    'field',
    'node',
    'og',
    'system',
    'user',
  ];

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();

    $this->installConfig(['og']);
    $this->installEntitySchema('node');
    $this->installEntitySchema('og_membership');
    $this->installSchema('system', 'sequences');

    NodeType::create([
      'name' => $this->randomString(),
      'type' => 'article',
    ])->save();
  }

  /**
   * Testing creation of og audience field using Drupal Console.
   */
  public function testNewGroup() {
    $helper = new HelperSet();
    $command = \Drupal::service('og.add_field');
    $commandTester = new CommandTester($command);
    $command->setHelperSet($helper);

    $fields = [
      [
        '--field_id' => 'og_audience',
        '--field_name' => 'og_group_ref',
        '--entity_type' => 'node',
        '--bundle' => 'article',
        '--target_entity' => 'node',
      ],
      [
        '--field_id' => 'og_audience',
        '--field_name' => 'og_audience',
        '--entity_type' => 'node',
        '--bundle' => 'article',
        '--target_entity' => 'node',
      ],
    ];

    foreach ($fields as $field) {
      $commandTester->execute($field);
    }

    $field_names = \Drupal::service('og.group_audience_helper')->getAllGroupAudienceFields('node', 'article');
    $this->assertEquals(['og_group_ref', 'og_audience'], array_keys($field_names));
  }

}
