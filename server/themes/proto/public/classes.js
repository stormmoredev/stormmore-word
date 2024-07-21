class StormmoreCommunityComponent extends StormComponent{
    name = "Michał";
    num = 1;
    rabbits;

    click() {
        console.log(this.num);
    }

    incrementRabbits() {
        this.rabbits++;
        this.set('rabbits', this.rabbits);
    }

    keypress(e) {
        console.log(e);
        console.log('key pressed ');
    }
}