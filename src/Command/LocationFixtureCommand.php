<?php

declare(strict_types=1);

namespace Netosoft\LocationBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Netosoft\LocationBundle\Entity\City;
use Netosoft\LocationBundle\Entity\Country;
use Netosoft\LocationBundle\Entity\County;
use Netosoft\LocationBundle\Entity\District;
use Netosoft\LocationBundle\Entity\Region;
use Netosoft\LocationBundle\Repository\CityRepository;
use Netosoft\LocationBundle\Repository\CountryRepository;
use Netosoft\LocationBundle\Repository\CountyRepository;
use Netosoft\LocationBundle\Repository\DistrictRepository;
use Netosoft\LocationBundle\Repository\RegionRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\OutputStyle;
use Symfony\Component\Console\Style\SymfonyStyle;
use Webmozart\Assert\Assert;

final class LocationFixtureCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $manager,
        private CityRepository $cityRepo,
        private CountryRepository $countryRepo,
        private CountyRepository $countyRepo,
        private RegionRepository $regionRepo,
        private DistrictRepository $districtRepo,
    ) {
        parent::__construct(null);
    }

    protected function configure(): void
    {
        $this->setName('location:load-fixtures');
        $this->setDescription('Load fixture for locations (country, region, county and city).');
        $this->addArgument('entity', InputArgument::REQUIRED, 'country, region, county, city or district', null);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new SymfonyStyle($input, $output);

        $entity = $input->getArgument('entity');
        Assert::stringNotEmpty($entity);

        switch ($entity) {
            case 'country':
                $style->title('Load countries');
                $this->loadCountries($style, $this->manager);
                break;
            case 'region':
                $style->title('Load regions');
                $this->loadRegions($style, $this->manager);
                break;
            case 'county':
                $style->title('Load counties');
                $this->loadCounties($style, $this->manager);
                break;
            case 'city':
                $style->title('Load cities');
                $this->loadCities($style, $this->manager);
                break;
            case 'district':
                $style->title('Load districts');
                $this->loadDistricts($style, $this->manager);
                break;
            default:
                $style->error(\sprintf('"%s" unknown entity', $entity));
                break;
        }

        return 0;
    }

    protected function loadCountries(OutputStyle $style, EntityManagerInterface $manager): void
    {
        $csvName = __DIR__.'/../../Resources/fixtures/countries.csv';
        if (false !== ($handle = \fopen($csvName, 'r'))) {
            $progress = $style->createProgressBar($this->countLines($csvName));

            while (false !== ($data = \fgetcsv($handle, 0, ';'))) {
                if (null === $data) {
                    continue;
                }

                [$id, $name, $slug, $prefix, $isoCode] = $data;

                $country = $this->countryRepo->find($id);
                if (null === $country) {
                    $id = (int) $id;
                    $country = new Country(
                        name: $name,
                        isoCode: $isoCode,
                        slug: $slug,
                        prefix: $prefix,
                    );
                    $country->setId($id);
                }

                $country->setName($name);
                $country->setSlug($slug);
                $country->setPrefix($prefix);
                $country->setIsoCode($isoCode);

                $manager->persist($country);

                $progress->setMessage($name);
                $progress->advance();
            }
            \fclose($handle);
            $manager->flush();

            $progress->finish();
            $style->writeln('');
        } else {
            throw new \RuntimeException('Failed to parse countries');
        }
    }

    protected function loadRegions(OutputStyle $style, EntityManagerInterface $manager): void
    {
        $csvName = __DIR__.'/../../Resources/fixtures/regions.csv';
        if (false !== ($handle = \fopen($csvName, 'r'))) {
            $progress = $style->createProgressBar($this->countLines($csvName));
            while (false !== ($data = \fgetcsv($handle, 0, ';'))) {
                if (null === $data) {
                    continue;
                }
                [$id, $countryId, $name, $slug, $code, $prefix] = $data;

                $country = $this->countryRepo->find($countryId);
                Assert::notNull($country);

                $region = $this->regionRepo->find($id);
                if (null === $region) {
                    $id = (int) $id;
                    $region = new Region(
                        country: $country,
                        name: $name,
                        slug: $slug,
                        code: $code,
                        prefix: $prefix,
                    );
                    $region->setId($id);
                }

                $region->setCountry($country);
                $region->setName($name);
                $region->setSlug($slug);
                $region->setCode($code);
                $region->setPrefix($prefix);

                $manager->persist($region);

                $progress->setMessage($name);
                $progress->advance();
            }
            \fclose($handle);

            $manager->flush();

            $progress->finish();
            $style->writeln('');
        } else {
            throw new \RuntimeException('Failed to parse regions');
        }
    }

    protected function loadCounties(OutputStyle $style, EntityManagerInterface $manager): void
    {
        $csvName = __DIR__.'/../../Resources/fixtures/counties.csv';
        if (false !== ($handle = \fopen($csvName, 'r'))) {
            $progress = $style->createProgressBar($this->countLines($csvName));
            while (false !== ($data = \fgetcsv($handle, 0, ';'))) {
                if (null === $data) {
                    continue;
                }
                [$id, $regionId, $name, $code, $slug, $prefix] = $data;

                $region = $this->regionRepo->find($regionId);
                Assert::notNull($region);

                $county = $this->countyRepo->find($id);
                if (null === $county) {
                    $id = (int) $id;
                    $county = new County(
                        region: $region,
                        name: $name,
                        slug: $slug,
                        code: $code,
                        prefix: $prefix,
                    );
                    $county->setId($id);
                }

                $county->setRegion($region);
                $county->setName($name);
                $county->setSlug($slug);
                $county->setCode($code);
                $county->setPrefix($prefix);

                $manager->persist($county);

                $progress->setMessage($name);
                $progress->advance();
            }
            \fclose($handle);

            $manager->flush();

            $progress->finish();
            $style->writeln('');
        } else {
            throw new \RuntimeException('Failed to parse counties');
        }
    }

    protected function loadCities(OutputStyle $style, EntityManagerInterface $manager): void
    {
        $csvName = __DIR__.'/../../Resources/fixtures/cities.csv';
        if (false !== ($handle = \fopen($csvName, 'r'))) {
            $progress = $style->createProgressBar($this->countLines($csvName));
            while (false !== ($data = \fgetcsv($handle, 0, ';'))) {
                if (null === $data) {
                    continue;
                }

                [$id, $countyCode, $slug, $name1, $name, $zipcode, $hasDistricts, $isCounty] = $data;

                $county = $this->countyRepo->findOneByCode($countyCode);
                Assert::notNull($county);

                $city = $this->cityRepo->find($id);
                if (null === $city) {
                    $id = (int) $id;
                    $city = new City(
                        county: $county,
                        name: $name,
                        slug: $slug,
                        zipcode: $zipcode,
                        isCounty: (bool) $isCounty,
                    );
                    $city->setId($id);
                }

                $city->setCounty($county);
                $city->setName($name);
                $city->setSlug($slug);
                $city->setZipcode($zipcode);
                $city->setIsCounty((bool) $isCounty);

                $manager->persist($city);

                $progress->setMessage(\sprintf('%s (%s)', $name, $zipcode));
                $progress->advance();
            }
            \fclose($handle);

            $manager->flush();

            $progress->finish();
            $style->writeln('');
        } else {
            throw new \RuntimeException('Failed to parse cities');
        }
    }

    protected function loadDistricts(OutputStyle $style, EntityManagerInterface $manager): void
    {
        $csvName = __DIR__.'/../../Resources/fixtures/districts.csv';
        if (false !== ($handle = \fopen($csvName, 'r'))) {
            $progress = $style->createProgressBar($this->countLines($csvName));
            while (false !== ($data = \fgetcsv($handle, 0, ';'))) {
                if (null === $data) {
                    continue;
                }

                [$id, $cityId, $slug, $name, $zipcode] = $data;
                $city = $this->cityRepo->find($cityId);
                Assert::notNull($city);

                $district = $this->districtRepo->find($id);

                if (null === $district) {
                    $id = (int) $id;
                    $district = new District(
                        city: $city,
                        name: $name,
                        slug: $slug,
                        zipcode: $zipcode,
                    );
                    $district->setId($id);
                }

                $district->setCity($city);
                $district->setName($name);
                $district->setSlug($slug);
                $district->setZipcode($zipcode);

                $manager->persist($district);

                $progress->setMessage(\sprintf('%s (%s)', $name, $zipcode));
                $progress->advance();
            }
            \fclose($handle);

            $manager->flush();

            $progress->finish();
            $style->writeln('');
        } else {
            throw new \RuntimeException('Failed to parse districts');
        }
    }

    /** @psalm-pure */
    private function countLines(string $file): int
    {
        return \count(\file($file));
    }
}
