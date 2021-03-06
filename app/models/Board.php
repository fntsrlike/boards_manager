<?php

class Board extends Eloquent{

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'boards';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array();

	protected $fillable = array('type', 'code', 'description');

	public function getUsingStatus($from='', $end='')
	{
		if ( empty($from) or empty($end) ) {
			$from = $end = date("Y-m-d") ;
		}

		if ( $this->type == 'stairs' ) {
			if ( Auth::check() ) {
				$record = ApplyRecord::date($from, $end)
					->where('board_id', $this->id)
					->where('user_id', Auth::id())
					->first();

				return is_null($record) ? false : $record->id;
			}
			return false;
		}

		$record = ApplyRecord::date($from, $end)
			->where('board_id', '=', $this->id)
			->orderBy('post_from', 'desc')
			->first();

		return is_null($record) ? false : $record->id;
	}

	public function scopeCode($query, $code)
	{
		return $query->where('code', $code)->first();
	}

	public function scopeIsUsing($query, $from='', $end='')
	{
		if ( empty($from) or empty($end) ) {
			$from = $end = date("Y-m-d") ;
		}

		$records = ApplyRecord::date($from, $end);

		$using_boards = array_unique($records->lists('board_id'));

		return $query->whereIn('id', $using_boards);

	}

	public function scopeIsEmpty($query, $from='', $end='')
	{
		if ( empty($from) or empty($end) ) {
			$from = $end = date("Y-m-d");
		}

		$records = ApplyRecord::date($from, $end);

		$using_boards = array_unique($records->lists('board_id'));

		return $query->whereNotIn('id', $using_boards);

	}
}
