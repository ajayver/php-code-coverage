<?php
namespace LearnDash\Core\Modules\REST\Documentation_Migration\OpenAPI\Endpoints\Quizzes;

class Quiz_Statistics_Questions
{
    public function score(int $value): int
    {
        if ($value > 10) {
            return 4;
        }

        if ($value > 5) {
            return 3;
        }

        if ($value > 0) {
            return 2;
        }

        return 1;
    }
}
