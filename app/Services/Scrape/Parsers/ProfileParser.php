<?php

namespace App\Services\Scrape\Parsers;

use App\Exceptions\Scrape\ExpectedFieldNotFoundException;
use Dom\HTMLDocument;

class ProfileParser implements Parser
{
    public function parse(mixed $content): array
    {
        $dom = HTMLDocument::createFromString($content);

        $username = $this->getUsername($dom);
        $name = $this->getName($dom);
        $bio = $this->getBio($dom);
        $likes = $this->getLikes($dom);

        return [
            'username' => $username,
            'name' => $name,
            'bio' => $bio,
            'likes' => $likes,
        ];
    }

    private function getUsername(HTMLDocument $dom): string
    {
        $node = $dom->querySelector('.b-profile__names > .b-username-row > .g-user-realname__wrapper > .g-user-username');
        throw_if(empty($node), ExpectedFieldNotFoundException::class, 'Username');

        $username = $node->textContent;
        return trim(str_replace('@', '', $username));
    }

    private function getName(HTMLDocument $dom): string
    {
        $node = $dom->querySelector('.b-profile__names > .b-username-row > .b-username > .g-user-name');
        throw_if(empty($node), ExpectedFieldNotFoundException::class, 'Name');

        $name = $node->textContent;
        return trim($name);
    }

    private function getBio(HTMLDocument $dom): string
    {
        $node = $dom->querySelector('.b-user-info__text');
        throw_if(empty($node), ExpectedFieldNotFoundException::class, 'Bio');

        $bio = $node->textContent;
        return trim(strip_tags($bio));
    }

    private function getLikes(HTMLDocument $dom): int
    {
        $node = $dom->querySelector('.b-profile__sections__link > svg[data-icon-name="icon-like"] + span.b-profile__sections__count');
        throw_if(empty($node), ExpectedFieldNotFoundException::class, 'Likes');

        $likes = $node->textContent;
        $likes = trim($likes);

        $suffix = substr($likes, -1);

        $multipliers = [
            'K' => 1_000,
            'M' => 1_000_000,
            'B' => 1_000_000_000,
        ];

        if (isset($multipliers[$suffix])) {
            $number = (float) substr($likes, 0, -1);

            return (int) ($number * $multipliers[$suffix]);
        }

        return (int) $likes;
    }
}
