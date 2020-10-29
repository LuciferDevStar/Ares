<?php declare(strict_types=1);

namespace h4kuna\Ares;

use Salamium\Testinium;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

class AresTest extends \Tester\TestCase
{

	/**
	 * @throws \h4kuna\Ares\Exceptions\IdentificationNumberNotFoundException
	 */
	public function testNotExists(): void
	{
		(new Ares)->loadData('36620751');
	}


	public function testFreelancer(): void
	{
		$ares = new Ares;
		$in = '87744473';
		/* @var $data Data */
		$data = (string) $ares->loadData($in);
		// Testinium\File::save($in . '.json', (string) $data);
		Assert::same(Testinium\File::load($in . '.json'), $data);
	}


	public function testMerchant(): void
	{
		$ares = new Ares;
		$in = '27082440';
		/* @var $data Data */
		$data = (string) $ares->loadData($in);
		// Testinium\File::save($in . '.json', (string) $data);
		Assert::same(Testinium\File::load($in . '.json'), $data);
	}


	public function testMerchantInActive(): void
	{
		$ares = new Ares;
		$in = '25596641';
		/* @var $data Data */
		$data = json_encode($ares->loadData($in));
		// Testinium\File::save($in . '.json', (string) $data);
		Assert::same(Testinium\File::load($in . '.json'), $data);
	}


	public function testHouseNumber(): void
	{
		$ares = new Ares;
		$in = '26713250';
		/* @var $data Data */
		$data = json_encode($ares->loadData($in));
		// Testinium\File::save($in . '.json', (string) $data);
		Assert::same(Testinium\File::load($in . '.json'), $data);
	}


	public function testToArray(): void
	{
		$ares = new Ares;
		$data = $ares->loadData('87744473');
		Assert::same('Milan Matějček', $data->company);

		$names = [];
		foreach (self::allPropertyRead($data) as $value) {
			if (!preg_match('~\$(?P<name>.*)~', $value, $find)) {
				throw new \RuntimeException('Bad annotation property-read od Data class: ' . $value);
			}
			Assert::true($data->exists($find['name']));
			$names[$find['name']] = true;
		}

		Assert::same([], array_diff_key($data->getData(), $names));

		Assert::same([
			"620",
			"461",
			"471",
			"73110",
			"7490",
		], $data->nace);

		Assert::type('array', $data->toArray());
		Assert::same([
			'c' => 'Milan Matějček',
			'company' => true,
			'city' => 'Mladá Boleslav',
		],
			$data->toArray(['company' => 'c', 'is_person' => 'company', 'city' => null]));
	}


	/**
	 * @throws \h4kuna\Ares\Exceptions\IdentificationNumberNotFoundException
	 */
	public function testNoIn(): void
	{
		(new Ares)->loadData('123');
	}


	public function testForeingPerson(): void
	{
		$data = (new Ares)->loadData('6387446');
		Assert::true($data->is_person);
	}


	/**
	 * @return array<string>
	 */
	private static function allPropertyRead(Data $data): array
	{
		$doc = (new \ReflectionClass($data))->getDocComment();
		if ($doc === false) {
			throw new \RuntimeException();
		}

		preg_match_all('/@property-read *(?P<propertyRead>.+)/', $doc, $match);
		return $match['propertyRead'];
	}

}

(new AresTest)->run();
