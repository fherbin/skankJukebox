<?php
declare(strict_types=1);

namespace App\Client\MusicBrainz;

enum MusicBrainzEntityEnum: string
{
    public const RECORDING_CASE_VALUE = 'recording';
    public const RELEASE_CASE_VALUE = 'release';
    public const ARTIST_CASE_VALUE = 'artist';

    case RECORDING = self::RECORDING_CASE_VALUE;
    case RELEASE = self::RELEASE_CASE_VALUE;
    case ARTIST = self::ARTIST_CASE_VALUE;
}