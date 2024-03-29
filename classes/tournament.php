<?php
class Tournament{
	private $id;
	private $name;
	private $startdate;
	private $description;
	private $main_image;
	private $fb_link;
	private $smashgg_link;

	public function __construct(){
	}
	public static function fromName($name){
		$db = PDOFactory::getConnection();
		$stmt = $db->prepare('SELECT * FROM tournament WHERE name = ?');
		$stmt->execute([$name]);
		return $stmt->fetch();
	}
	public static function newTournament($name, $startdate, $description, $main_image, $fb_link, $smashgg_link, $user){
		$tourney = new Tournament();
		$tourney->id = uniqid('', true);
		$tourney->name = $name;
		$tourney->startdate = $startdate;
		$tourney->description = $description;
		$tourney->main_image = $main_image;
		$tourney->fb_link = $fb_link;
		$tourney->smashgg_link = $smashgg_link;
		$db = PDOFactory::getConnection();
		$stmt = $db->prepare('INSERT INTO tournament (id, name, startdate, description, main_image, fb_link, smashgg_link) VALUES (?,?,?,?,?,?,?');
		$stmt->execute([$tourney->id, $tourney->name, $tourney->startdate, $tourney->description, $tourney->main_image, $tourney->fb_link, $tourney->smashgg_link]);

		$to = $db->prepare('INSERT INTO tournament_organizers (user_id, touranment_id, title) VALUES(?,?,?)');
		$to->execute([$user, $tourney->id, 'Head Organizer']);
	}
	public function saveTournament(){
		$db = PDOFactory::getConnection();
		$stmt = $db->prepare('UPDATE tournament SET name = ?, startdate = ?, description = ?, main_image = ?, fb_link = ?, smashgg_link = ? WHERE id = ?');
		$stmt->execute([$this->name, $this->startdate, $this->description, $this->main_image, $this->fb_link, $this->smashgg_link, $this->id]);
		
	}
	public static function getAllTournaments(){
		$db = PDOFactory::getConnection();
		$today = date("Y-m-d");
		$stmt = $db->prepare('SELECT * FROM tournament WHERE DATE(startdate) >= ? ORDER BY startdate');
		$stmt->setFetchMode(PDO::FETCH_CLASS, 'Tournament');
		$stmt->execute([$today]);
		$tourneys = $stmt->fetchAll();
		return $tourneys;
	}
	public static function getAllPastTournaments(){
		$db = PDOFactory::getConnection();
		$today = date("Y-m-d");
		$stmt = $db->prepare('SELECT * FROM tournament WHERE DATE(startdate) <= ? ORDER BY startdate DESC');
		$stmt->setFetchMode(PDO::FETCH_CLASS, 'Tournament');
		$stmt->execute([$today]);
		$tourneys = $stmt->fetchAll();
		return $tourneys;

	}
	public function getID(){
		return $this->id;
	}
	public function getName(){
		return $this->name;
	}
	public function getStartDate(){
		return $this->startdate;
	}
	public function setStartDate($date){
		$this->startdate = $date;
	}
	public function getDescription(){
		return $this->description;
	}
	public function setDescription($desc){
		$this->description = $desc;
	}
	public function getMainImage(){
		return $this->main_image;
	}
	public function setMainImage($image_loc){
		$this->main_image = $image_loc;
	}
	public function getLink(){
		return '/tourney/tournament.php?name=' . $this->name;
	}
	public function getEvents(){
		$db = PDOFactory::getConnection();
		$stmt = $db->prepare('SELECT * FROM events WHERE tournament_id = ?');
		$stmt->setFetchMode(PDO::FETCH_CLASS, 'Event');
		$stmt->execute([$this->id]);
		return $stmt->fetchAll();
	}
	public function getGames(){
		$db = PDOFactory::getConnection();
		$stmt = $db->prepare('SELECT * FROM events WHERE tournament_id = ?');
		$stmt->setFetchMode(PDO::FETCH_CLASS, 'Event');
		$stmt->execute([$this->id]);
		$events = $stmt->fetchAll();
		$games = array();
		foreach($events as $event){
			$code = $event->getGameCode();
			array_push($games, $code);
		}
		return array_unique($games);
	}
	public function tournamentDisplay(){
		$return = '<div class="tournament-box">';
	}
	public function __toString(){
		return $this->name;
	}
}
class Event{
	private $id;
	private $tournament_id;
	private $game_code;
	private $team_size;
	private $event_name;

	public function __construct(){
	}
	public static function newEvent($tournament_id,  $game_code, $team_size, $event_name){
		$self = new self();
		$self->id = uniqid('', true);
		$self->tournament_id = clean($tournament_id);
		$self->game_code = $game_code;
		$self->team_size = $team_size;
		$self->event_name = $event_name;
		$db = PDOFactory::getConnection();
		$stmt = $db->prepare('INSERT INTO events (id, tournament_id, game_code, team_size, event_name) VALUES (?, ?, ?, ?, ?');
		$stmt->execute([$self->id, $self->tournament_id, $self->game_code, $self->team_size, $self->event_name]);
		return $self;
	}
	public function getTournamentID(){
		return $this->tournament_id;
	}
	public function getEntrantListID(){
		return $this->entrant_list_id;
	}
	public function getGameCode(){
		return $this->game_code;
	}
	public function setGameCode($code){
		$this->game_code = $code;
	}
	public function getTeamSize(){
		return $this->team_size;
	}
	public function getEventName(){
		return $this->event_name;
	}
	public function updateEvent(){
		$db = PDOFactory::getConnection();
		$stmt = $db->prepare('UPDATE events SET game_code = ?, team_size = ?, event_name = ? WHERE id = ?');
		$stmt->execute([$this->game_code, $this->team_size, $this->event_name, $this->id]);
	}
}