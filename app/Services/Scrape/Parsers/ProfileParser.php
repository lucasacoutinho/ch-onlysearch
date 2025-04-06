<?php

namespace App\Services\Scrape\Parsers;

use Dom\HTMLDocument;

class ProfileParser implements Parser
{
    public function parse(mixed $content): array
    {
        $dom = HTMLDocument::createFromString($content);

        $username = $this->getUsername($dom);
        $name     = $this->getName($dom);
        $bio      = $this->getBio($dom);
        $likes    = $this->getLikes($dom);

        return [
            'username' => $username,
            'name'     => $name,
            'bio'      => $bio,
            'likes'    => $likes,
        ];
    }

    private function getUsername(HTMLDocument $dom): string
    {
        $node = $dom->querySelector('#content > div.l-wrapper.m-guest > div.l-wrapper__holder-content > div > div.l-profile-container > div > div.b-profile__header__user.g-sides-gaps > div.b-profile__user.d-flex.align-items-start > div.b-profile__names.mw-0.w-100.mw-100 > div:nth-child(2) > div.g-user-realname__wrapper.m-nowrap-text > div');
        $username = $node->textContent;
        return trim(str_replace('@', '', $username));
    }

    private function getName(HTMLDocument $dom): string
    {
        $node = $dom->querySelector('#content > div.l-wrapper.m-guest > div.l-wrapper__holder-content > div > div.b-compact-header.g-sides-gaps.js-compact-sticky-header > div > div.b-compact-header__user.mw-0.flex-fill-1 > div.b-username-row.m-gap-clear > div > div');
        $name = $node->textContent;
        return trim($name);
    }

    private function getBio(HTMLDocument $dom): string
    {
        $node = $dom->querySelector('#content > div.l-wrapper.m-guest > div.l-wrapper__holder-content > div > div.l-profile-container > div > div.b-profile__header__user.g-sides-gaps > div.b-profile__content > div.b-user-info.m-mb-sm > div > div > div > p');
        $bio = $node->textContent;
        return trim(strip_tags($bio));
    }

    private function getLikes(HTMLDocument $dom): int
    {
        $node = $dom->querySelector('#content > div.l-wrapper.m-guest > div.l-wrapper__holder-content > div > div.b-compact-header.g-sides-gaps.js-compact-sticky-header > div > div.b-compact-header__user.mw-0.flex-fill-1 > div.b-profile__sections.d-flex.align-items-center > div > div:nth-child(4) > span > span');
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
