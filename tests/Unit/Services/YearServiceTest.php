<?php

# php artisan test tests/Unit/Services/YearServiceTest.php

use App\Models\Year\Year;
use App\Services\Question\YearService;

describe("Year Service", function () {
     beforeEach(function () {
          $this->service = new YearService();
     });

     afterEach(function () {
          Mockery::close();
     });

     test("getById must return year by id", function () {
          $content = Year::factory()->create();

          $result = $this->service->getById($content->id);

          $this->assertEquals($content->id, $result->id);
          $this->assertEquals($content->name, $result->name);
     });
});
