<?php

namespace App\Widgets;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use TCG\Voyager\Facades\Voyager;
use App\Restaurant;

class Restaurants extends BaseDimmer
{
    /**
     * The configuration array.
     *
     * @var array
     */
    protected $config = [];

    /**
     * Treat this method as a controller action.
     * Return view() or other content to display.
     */
    public function run()
    {
        $count = \App\Models\Restaurant::count();
        $string = trans_choice('Restaurants', $count);

        return view('voyager::dimmer', array_merge($this->config, [
            'icon'   => 'voyager-truck',
            'title'  => "{$count} {$string}",
            'text'   => 'You have '.$count.' Restaurants in your database.',
            'button' => [
                'text' => 'View All Restaurants',
                'link' => route('voyager.restaurants.index'),
            ],
            'image' => asset('images/widgets/restaurant.jpg'),
        ]));
    }

    /**
     * Determine if the widget should be displayed.
     *
     * @return bool
     */
    public function shouldBeDisplayed()
    {
        return Auth::user()->can('browse', Voyager::model('Page'));
    }
}
