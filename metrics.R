ComputeMetrics <- function()
{
#metrics
cat(paste("Step 5 of 5 initiated : Computing metrics...\n"));
suppressMessages(library(stringdist))

rulesdf = read.csv('rules.csv')
medlookup = read.csv('MedicationsToConsider.csv')

precisionarray = c()
recallarray = c()
fmeasurearray = c()
accuracyarray = c()

actualmedication = c()
recommendedmedication = c()
actualmedicationarray = c()
recommendedmedicationarray = c()
RiskScoreWithoutMedicationsarray = c()
RiskScoreWithMedicationsarray = c()

testdf = testdf[which(testdf$thirtyday==0),]
testdf = testdf[1:10,]
eta = ((nrow(testdf))/100)*10
cat(paste(" Expected time to completion : ",eta,"seconds, ==",eta/60,"minute(s)...\n"))
cat(paste(" Process to complete around ",as.character(Sys.time()+eta),"\n\n"))
for(i in 1:nrow(testdf))
{
        #print(i)

        mytestrow = testdf[i,]
        #matchrow = rulesdf[which.min(Reduce(`+`,Map(stringdist,rulesdf, mytestrow, method='jaccard'))),]
        matchrow = rulesdf[which.min(Reduce(`+`,Map(stringdist,rulesdf[,-grep("MED.",names(rulesdf))], mytestrow[-grep("MED.",names(mytestrow))], method='jaccard'))),]

        tp = 0 ;fp = 0; tn = 0; fn = 0;
        actualmedication = c()
        recommendedmedication = c()

        #from 53rd column onwards, it's MED
        for(j in 73:122)
        {
                if((matchrow[j] == 1) & (mytestrow[j] == 1))
                {
                        tp = tp + 1;
                }else if((matchrow[j] == 1) & (mytestrow[j] == 0))
                {
                        fp = fp + 1;
                }else if((matchrow[j] == 0) & (mytestrow[j] == 1))
                {
                        fn = fn + 1;
                }else if((matchrow[j] == 0) & (mytestrow[j] == 0))
                {
                        tn = tn + 1;
                }

                if(matchrow[j]==1)
                {
                        if((length(recommendedmedication))==0)
                        {
                                recommendedmedication = as.character(medlookup[which(medlookup$CODE==names(matchrow)[j]),"DESCRIPTION"])
                        }else
                        {
                                recommendedmedication = paste(recommendedmedication,as.character(medlookup[which(medlookup$CODE==names(matchrow)[j]),"DESCRIPTION"]),sep="||")
                        }
                }
	        if(mytestrow[j]==1)
                {
                        if(length(actualmedication) == 0)
                        {
                                actualmedication = as.character(medlookup[which(medlookup$CODE==names(mytestrow)[j]),"DESCRIPTION"])
                        }else
                        {
                                actualmedication = paste(actualmedication,as.character(medlookup[which(medlookup$CODE==names(mytestrow)[j]),"DESCRIPTION"]),sep="||")
                        }
                }
        }

	if((length(actualmedication))==0)
	{
		actualmedication = "NONE"
	}
	if((length(recommendedmedication))==0)
	{
		recommendedmedication = "NONE"
	}
		
        if((tp == 0) & (fp == 0))
        {
		#cat(paste("here (tp == 0) & (fp == 0), actualmedication==",actualmedication,"recommendedmedication==",recommendedmedication,"\n"))
                precision = 0;
		recall = 0;
        }else
        {
                precision = tp/(tp+fp)
        }

        if((tp == 0) & (fn == 0))
        {
		#cat(paste("here (tp == 0) & (fn == 0), actualmedication==",actualmedication,"recommendedmedication==",recommendedmedication,"\n"))
		precision = 1;
                recall = 1;
        }else
        {
                recall = tp/(tp+fn)
        }
		
	if((precision == 0) & (recall == 0))
	{
		fmeasure = 0;
	}else
	{
		fmeasure = (2*precision*recall)/(precision+recall)
	}
        accuracy = (tp+tn)/(tp+tn+fp+fn)

        precisionarray = c(precisionarray,precision)
        recallarray = c(recallarray,recall)
        fmeasurearray = c(fmeasurearray,fmeasure)
        accuracyarray = c(accuracyarray,accuracy)

        actualmedicationarray = c(actualmedicationarray,actualmedication)
        recommendedmedicationarray = c(recommendedmedicationarray,recommendedmedication)
        RiskScoreWithoutMedicationsarray = c(RiskScoreWithoutMedicationsarray,matchrow$RiskScoreWithoutMedications)
        RiskScoreWithMedicationsarray = c(RiskScoreWithMedicationsarray,matchrow$RiskScoreWithMedications)
}

testdf$precision = precisionarray
testdf$recall = recallarray
testdf$accuracy = accuracyarray
testdf$fmeasure = fmeasurearray
testdf$actualmedication = actualmedicationarray
testdf$recommendedmedication = recommendedmedicationarray
testdf$RiskScoreWithoutMedications = RiskScoreWithoutMedicationsarray
testdf$RiskScoreWithMedications = RiskScoreWithMedicationsarray

cat(paste(" Precision==",mean(precisionarray),"\n"))
cat(paste(" Recall==",mean(recallarray),"\n"))
cat(paste(" Fmeasure==",mean(fmeasurearray),"\n"))
cat(paste(" Accuracy==",mean(accuracyarray),"\n"))

#write results to results.csv
#testdf = testdf[which(testdf$Readmit == 0),-c((grep("CPT.",names(testdf))),(grep("MED.",names(testdf))))]
write.csv(testdf,'results.csv',row.names=FALSE)
save.image(file = './steps/step5.RData')
cat(paste("Step 5 of 5 completed : Metrics and results generated, See results.csv...\n\n"));

return (mean(precisionarray)*100);
}