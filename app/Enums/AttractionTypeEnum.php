<?php

namespace App\Enums;

enum AttractionTypeEnum: string
{
    case NATURAL = 'natural';
    case MAN_MADE = 'man_made';
    case CULTURAL = 'cultural';
    case SPORT = 'sport';
    case EVENTS = 'events';
    case LEISURE = 'leisure';

    /**
     * Get all enum values as array.
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get the label for the enum value.
     */
    public function label(): string
    {
        return match($this) {
            self::NATURAL => 'Natural',
            self::MAN_MADE => 'Man-made',
            self::CULTURAL => 'Cultural',
            self::SPORT => 'Sport',
            self::EVENTS => 'Events',
            self::LEISURE => 'Leisure',
        };
    }

    /**
     * Get the description for the enum value.
     */
    public function description(): string
    {
        return match($this) {
            self::NATURAL => 'Beaches, Mountains, National Parks, Forests, Lakes, Waterfalls, Islands, Wildlife Reserves, Canyons, Deserts, Rivers, Volcanoes, Caves',
            self::MAN_MADE => 'Historical Sites, Monuments, Museums, Art Galleries, Castles/Forts, Ancient Temples, Skyscrapers, Bridges, Zoos/Aquaria, Botanical Gardens, Theme Parks, Amusement Parks, Factories',
            self::CULTURAL => 'Heritage Sites, Ethnic Enclaves, Living History Museums, Religious Temples, Festivals, Public Art, Libraries, Theaters, Ethnic Communities, Industrial Heritage',
            self::SPORT => 'Stadiums, Ski Resorts, Golf Courses, Adventure Parks, Extreme Sports Sites, Sailing Regattas, Formula 1 Tracks, Climbing Walls, Surf Spots',
            self::EVENTS => 'Carnivals, Concerts, Exhibitions, Cultural Festivals, Religious Events, Sports Events, Industrial Tours, Trade Shows',
            self::LEISURE => 'Resorts, Shopping Malls, Spas, Nightlife Venues, Honeymoon Spots, Wellness Centers, Cruise Ports, Gambling Casinos',
        };
    }

    /**
     * Get options for select inputs.
     */
    public static function getOptions(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }
        return $options;
    }

    /**
     * Get options with descriptions for select inputs.
     */
    public static function getOptionsWithDescriptions(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = [
                'label' => $case->label(),
                'description' => $case->description(),
            ];
        }
        return $options;
    }
}
