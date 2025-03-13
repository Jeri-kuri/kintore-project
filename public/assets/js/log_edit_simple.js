function LogviewModel(){
    var self = this;

    self.exercise = ko.observable("");
    self.weight = ko.observable(0);
    self.reps = ko.observable(0);

     // data binding
     self.exercise = ko.observable(initialdata.exercise);
     self.weight = ko.observable(initialdata.weight);
     self.reps = ko.observable(initialdata.reps);
 
     self.showWeightError = ko.observable(false);
     self.showRepError = ko.observable(false);
 

    self.isEditing=ko.observable(false);

    self.saveLog = function(){
        console.log("Saved:", {
            exercise: self.exercise(),
            weight: self.weight(),
            reps: self.reps()
        });
    };

    self.editLog = function () {
        self.isEditing(true);
        self.editedWeight(self.weight());
        self.editedReps(self.reps());
        console.log(self.isEditing())
    }

     //編集のキャンセル
     self.cancelEdit = function() {
        self.isEditing(false);
    }
}

ko.applyBindings(new LogviewModel({ weight: set.weight, reps: set.reps }));