<?php

namespace Tests\Feature;

//use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetProductTest extends TestCase
{
    private function getRequest(string $url)
    {
        $responseV1 = $this->get("/api/{$url}");
        $responseV1->assertStatus(200);
        $jsonV1 = $responseV1->json();
        $this->assertArrayHasKey('pages', $jsonV1);

        $responseV2 = $this->get("/api/v2/{$url}");
        $responseV2->assertStatus(200);
        $jsonV2 = $responseV2->json();
        $this->assertArrayHasKey('pages', $jsonV2);

        $this->assertEquals($jsonV1, $jsonV2);
        return $jsonV1;
    }


    public function test_getting_default_reponse(): void
    {
        $json = $this->getRequest('products');

        $this->assertEquals(3, $json['pages']);
        unset($json['pages']);

        $this->assertCount(8, $json);

        $this->assertIsArray($json[0]);

        $expectedProduct =  [
            "image" => "https://img.tmstor.es//28618.jpg",
            "id" => 28618,
            "artist" => "The Tests",
            "title" => "Signed Fender Stratocaster",
            "description" => "This fender stratocaster has been signed by each band member and was used in the recording of the new album. This is the only one available and is a collector's item! Don't miss out on claiming this piece of history. &pound;500 from this sale will be donated to the Test's favoured charity, 'Cancer Research'.",
            "price" => 1995,
            "format" => "merch",
            "release_date" => "2016-03-01",
        ];
        $this->assertEquals($expectedProduct, $json[0]);
    }

    public function test_getting_default_response_second_page(): void
    {
        $json = $this->getRequest('products?page=2');
        $this->assertEquals(3, $json['pages']);
        unset($json['pages']);

        $this->assertCount(8, $json);

        $this->assertIsArray($json[0]);

        $expectedProduct = [
            "image" => "https://img.tmstor.es//24621.jpg",
            "id" => 24621,
            "artist" => "The Tests",
            "title" => "Marathon Ladies T-Shirt",
            "description" => "\"It's a Marathon! Marathon!\"<br /><br />A t-shirt just for the ladies!",
            "price" => 12,
            "format" => "merch",
            "release_date" => null,
        ];

        $this->assertEquals($expectedProduct, $json[0]);
    }


    public function test_getting_numeric_missing_section(): void
    {
        $json = $this->getRequest('products/1');
        $this->assertEquals(['pages' => 0], $json);
    }

    public function test_getting_numeric_section(): void
    {
        $json = $this->getRequest('products/3253');
        $this->assertEquals(1, $json['pages']);
        unset($json['pages']);
        $this->assertCount(1, $json);
        $this->assertIsArray($json[0]);
        $expectedProduct = [
            "image" => "https://img.tmstor.es//28132.jpg",
            "id" => 28132,
            "artist" => "The Tests",
            "title" => "Test The Theory",
            "description" => "<span>Following on from the success of the last Album, The Test's are back with a new explosive sound described by MNE as 'Truly Original'.</span><br /><br /><span>The Test's themselves have expressed that this album is the best album they've produced in years.&nbsp;</span><br /><br /><span>Including several exclusive tracks available only on the album with guest features by greats like Jax and legendary guitarist Johnny Saturno this album is set to impress. - See more at: http://test.tmstore.co.uk/#sthash.SagiDcqV.dpuf</span>",
            "price" => 7,
            "format" => "download",
            "release_date" => '2016-03-01',
        ];

        $this->assertEquals($expectedProduct, $json[0]);
    }

    public function test_getting_string_section(): void
    {
        $json = $this->getRequest('products/Downloads');

        $this->assertEquals(1, $json['pages']);
        unset($json['pages']);
        $this->assertCount(1, $json);
        $this->assertIsArray($json[0]);
        $expectedProduct = [
            "image" => "https://img.tmstor.es//28132.jpg",
            "id" => 28132,
            "artist" => "The Tests",
            "title" => "Test The Theory",
            "description" => "<span>Following on from the success of the last Album, The Test's are back with a new explosive sound described by MNE as 'Truly Original'.</span><br /><br /><span>The Test's themselves have expressed that this album is the best album they've produced in years.&nbsp;</span><br /><br /><span>Including several exclusive tracks available only on the album with guest features by greats like Jax and legendary guitarist Johnny Saturno this album is set to impress. - See more at: http://test.tmstore.co.uk/#sthash.SagiDcqV.dpuf</span>",
            "price" => 7,
            "format" => "download",
            "release_date" => '2016-03-01',
        ];

        $this->assertEquals($expectedProduct, $json[0]);
    }
}
